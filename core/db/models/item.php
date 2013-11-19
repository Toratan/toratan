<?php
namespace core\db\models;
/**
 * General item entity
 */
abstract class item extends \ActiveRecord\Model
{
    static $validates_presence_of = array();
    /**
     * Get/Set item's table name
     * @var string
     */
    private $item_table_name;
    /**
     * Get/Set item's raw name
     * @var string
     */
    private $item_name;

    public function __construct(array $attributes = array(), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) 
    {
        # fetch the cache sig.
        $cache_sig = get_called_class();
        # create a new cache
        # don't use xCache it will overload the session file
        $fc = new \zinux\kernel\caching\fileCache(__CLASS__);
        # check the cache if the $cache_sig has loaded before
        if($fc->isCached($cache_sig))
            # just fetch from cache system
            $item_info = $fc->fetch ($cache_sig);
        else
        {
            # a fast class name fetching [ do not use (str/preg)_replace we need it to be fast ]
            $this->item_table_name =
                    # we need to use \ActiveRecord\Inflector to normalize our table in activerecord way
                    \ActiveRecord\Inflector::instance()->tableize(($this->item_name = substr($cache_sig, strrpos($cache_sig, "\\")+1)));
             # create an item info package
             $item_info = array("item_table_name" => $this->item_table_name, "item_name" => $this->item_name);
             # save the { namespace\class => class, table_name } comb.
             $fc->save($cache_sig, $item_info);
        }
        # load item's table name
        $this->item_table_name = $item_info["item_table_name"];
        # load item's name
        $this->item_name = $item_info["item_name"];
        # we have now fetched our item info
        # we will set our table name's to its proper value
        parent::$table_name = $this->item_table_name;
        # set a title validation 
        self::$validates_presence_of[] = array("{$this->item_name}_id", 'message' => 'cannot be blank!');
        # set an id validation
        self::$validates_presence_of[] = array("{$this->item_name}_title", 'message' => 'cannot be blank!');
        # after setting the table's name we go for parent contruction
        parent::__construct($attributes, $guard_attributes, $instantiating_via_find, $new_record);
        
    }
    /**
     * Destruct the item
     */
    public function __destruct()
    {
        # unset the static table name
        self::$table_name = "";
    }
    /**
     * The save procedure interface for item
     * @param boolean $validate should it validate the attribs
     * @throws \zinux\kernel\exceptions\invalideOperationException if duplication error happen
     * @throws \core\db\models\Exception if any other error happen
     */
    public function save($validate = true)
    {
        try
        {
            # try to save it
            parent::save($validate);
        }
        # cache if anything happened
        catch(\Exception $e)
        {
            # if it was a duplication error
            if(preg_match("#1062 Duplicate entry#i", $e->getMessage()))
                    # throw an invalid operation exception
                    throw new \zinux\kernel\exceptions\invalideOperationException("The item your are tring to create already exists!");
            # otherwise throw just as is
            else throw $e;
        }
        # to boost up the speed we don't put it in the try/catch to prevent to getting thrown twice
        # check if it is an invalid
        if($this->is_invalid())
        {
            # create an exception collector
            $ec = new \core\exceptions\exceptionCollection;
            foreach($this->errors->full_messages() as $error_msg)
            {
                # add the message as an exception in the collector
                $ec->addException(new \zinux\kernel\exceptions\dbException($error_msg));
            }
            # throw the exceptions
            $ec->ThrowCollected();
        }
    }
    /**
     * Get the current item's behavioral name
     * @return string
     */
    public function WhoAmI(){ return $this->item_name; }
    
    /**
     * Creates a new item in { title | body } datastructure
     * @param string $title the item's title
     * @param string $body the item's body
     * @param string $parent_id the item's parent id
     * @param string $owner_id the item's owner
     * @throws \zinux\kernel\exceptions\invalideArgumentException if title not string or be empty
     * @throws \zinux\kernel\exceptions\invalideOperationException if duplication problem raise during saving item to db
     * @throws \core\db\models\Exception if any other exception raised that didn't match with previous excepions
    */
    public function newItem($title, $body, $parent_id, $owner_id)
    {
        # set the title
        $this->{"{$this->item_name}_title"} = $title;
        # set the body
        $this->{"{$this->item_name}_body"} = $body;
        # set the user name
        $this->owner_id = $owner_id;
        # set the parent id
        $this->parent_id = $parent_id;
        # generate an id
        $this->{"{$this->item_name}_id"} = $this->generate_item_id($parent_id, $owner_id, $title);
        #save it
        $this->save();
       
    }
    /**
     * Fetches a single item from database
     * @param string $item_id item's id
     * @return item
     */
    public function fetch($item_id)
    {
        return $this->find($item_id);
    }
    /**
     * Fetches all sub-items under a parent directory with an owner
     * @param string $owner_id items' owner id
     * @param string $parent_id items' parent id
     * @return array of items
     */
    public function fetchItems($owner_id, $parent_id)
    {
        # returns all items with given owner and parent id
        return $this->find("all", array("conditions" => array("owner_id = ? AND parent_id = ?", $owner_id, $parent_id)));
    }
    /**
     * Edits an item
     * @param string $item_id the item's id
     * @param string $owner_id the item's owner id
     * @param string $title string the item's title
     * @param string $body the item's body
     * @param boolean $is_public should it be public or noy
     * @throws \core\db\exceptions\dbNotFound if the item not found
     */
    public function edit($item_id, $owner_id, $title, $body, $is_public = -1)
    {
        # fetch the item
        $item = $this->fetch($item_id);
        # check if item not found or the owner didn't matched
        if(!$item || $owner_id != $item->owner_id)
            throw new \core\db\exceptions\dbNotFound;
        # set the title
        $item->{"{$this->item_name}_title"} = $title;
        # set the body
        $item->{"{$this->item_name}_body"} = $body;
        # if is_public setted right
        if($is_public>-1 && $is_public<2)
            $item->is_public = $is_public;
        # generates new item id
        $item->{"{$this->item_name}_id"} = $this->generate_item_id($item->parent_id, $owner_id, $title);
        # save it
        $item->save();
    }
    /**
     * Generates item id based on passed arguments
     * @param string $parent_id
     * @param string $owner_id
     * @param string $title
     * @return string
     */
    public function generate_item_id($parent_id, $owner_id, $title)
    {
        # generate a hash-sum
        return \zinux\kernel\security\hash::Generate($parent_id.$title.$owner_id);
    }
}
