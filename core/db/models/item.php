<?php
namespace core\db\models;
/**
 * General item entity
 */
abstract class item extends \ActiveRecord\Model
{
    static $validates_numericality_of = array(
            array('is_public', 'less_than_or_equal_to' => 1, 'greater_than_or_equal_to' => 0),
            array('is_trash', 'less_than_or_equal_to' => 1, 'greater_than_or_equal_to' => 0),
    );
    # genaral presence validation container
    static $validates_presence_of;
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
        self::$validates_presence_of["id"] = array("{$this->item_name}_id", 'message' => 'cannot be blank!');
        # set an id validation
        self::$validates_presence_of["title"] = array("{$this->item_name}_title", 'message' => 'cannot be blank!');
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
     * @return item the create item
    */
    public function newItem($title, $body, $parent_id, $owner_id)
    {
        # normalizing the inputs
        $title = trim($title);
        $body = trim($body);
        # fetch the item's handler
        $item_class = get_called_class();
        # revoke an instance of item
        $item = new $item_class;
        # set the title
       $item->{"{$this->item_name}_title"} = $title;
        # set the body
       $item->{"{$this->item_name}_body"} = $body;
        # set the user name
       $item->owner_id = $owner_id;
        # set the parent id
       $item->parent_id = $parent_id;
        # by default it is not public
       $item->is_public = b'0';
        # by default it is not trash
       $item->is_trash = b'0';
        # generate an id
       $item->{"{$this->item_name}_id"} = $this->generate_item_id($parent_id, $owner_id, $title);
        #save it
       $item->save();
       # return the created item
       return $item;
    }
    /**
     * Fetches a single item from database
     * @param string $item_id item's id
     * @return item
     */
    public function fetch($item_id, $owner_id = NULL)
    {
        $cond = array("conditions" => array("{$this->item_name}_id = ?", $item_id));
        if($owner_id)
            $cond = array("conditions" => array("{$this->item_name}_id = ? AND owner_id = ?", $item_id, $owner_id));
        $item = $this->find($item_id, $cond);
        if(!$item)
            throw new \core\db\exceptions\dbNotFound("$this->item_name with ID=`$item_id` not found or you don't have the access premission!");
        return $item;
    }
    /**
     * Fetches all sub-items under a parent directory with an owner
     * @param string $owner_id items' owner id
     * @param string $parent_id items' parent id
     * @param boolean $is_public should it be public or not, pass -1 to no difference
     * @param boolean $is_trash should it be trashed or not, pass -1 to no difference
     * @return array of items
     */
    public function fetchItems($parent_id, $owner_id, $is_public = -1, $is_trash = -1)
    {
        # general conditions
        $item_cond = "owner_id = ? AND parent_id = ? ";
        $cond = array($item_cond, $owner_id, $parent_id);
        # if is public revoked
        if($is_public>-1 && $is_public<2)
        {
            # flag it
            $cond[0] .= "AND is_public =  ?";
            $cond[] = $is_public;
        }
        # if is trash revoked
        if($is_trash>-1 && $is_trash<2)
        {
            # flag it
            $cond[0] .= "AND is_trash =  ?";
            $cond[] = $is_trash;
        }
        # returns all items with given owner and parent id
        return $this->find("all", array("conditions" => $cond));
    }
    /**
     * Edits an item
     * @param string $item_id the item's id
     * @param string $owner_id the item's owner id
     * @param string $title string the item's title
     * @param string $body the item's body
     * @param boolean $is_public should it be public or not, pass -1 to no change
     * @param boolean $is_trash should it be trashed or not, pass -1 to no change
     * @throws \core\db\exceptions\dbNotFound if the item not found
     */
    public function edit($item_id, $owner_id, $title, $body, $is_public = -1, $is_trash = -1)
    {
        # the only time which exception could raise in this
        # method is when $title is empty
        # since we delete the item and re-create it
        # we don't want to wast time on restoring failed edition on title's emptiness
        if(empty($title) || !strlen($title))
        {
           # so we fore-playing the senario here
           throw new \zinux\kernel\exceptions\dbException("{$this->item_name} title cannot be blank!");
        }
        # fetch the deleting item, because we are going re-generate the item's ID
        $deleted_item = $this->fetch($item_id, $owner_id);
        if($owner_id == $deleted_item->owner_id && $title == $deleted_item->{"{$this->item_name}_title"} && $body == $deleted_item->{"{$this->item_name}_body"})
            $item = $deleted_item;
        else
        {
            # the only the body changed we don't need to delete the item because our item's ID generating
            # depends on them and by changing the body the item's ID won't change so we are OK by just
            # changing the item's body part
            if($title == $deleted_item->{"{$this->item_name}_title"})
            {
                # just change the body part
                $deleted_item->{"{$this->item_name}_body"} = $body;
                # consider it as a new item
                $item = $deleted_item;
            }
            # otherwise if item's title has been changed we need to create a new item
            # because by changing the title the ID will change too
            else
            {
                # creates a new item
                $item = $this->newItem($title, $body, $deleted_item->parent_id, $owner_id);
                # restore the creation time
                $item->created_at = $deleted_item->created_at;
                # if the creatation was success and no exception get thrown
                # delete the old item
                $this->delete($deleted_item->{"{$this->item_name}_id"}, $owner_id, 0);
            }
        }
        # modify the publicity of the item if necessary
        if($is_public==-1)
            $item->is_public = $deleted_item->is_public;
        else
            $item->is_public = $is_public;
        # modify the trash flag of the item if necessary
        if($is_trash==-1)
            $item->is_trash = $deleted_item->is_trash;
        else
            $item->is_trash = $is_trash;
        # save the item
        $item->save();
        # return the edited item
        return $item;
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
        return substr($owner_id, 0, 7).substr($parent_id, 0, 7).substr(sha1($title), 0, 7).\zinux\kernel\security\hash::Generate($title);
    }
    /**
     * Deletes an item
     * @param string $item_id the item's ID
     * @param string $owner_id the item's owner's ID
     * @param boolean $make_trash should just flag it as trash or delete it permanently
     * @return item the deleted item
     */
    public function delete($item_id, $owner_id, $make_trash = 1)
    {
        # fetch the item
        $item = $this->fetch($item_id, $owner_id);
        # if we should flag it as trash
        if($make_trash)
        {
            # so be it
            $item->is_trash = 1;
            $item->save();
        }
        else
            # detele permanent
            $item->delete_all(array("conditions" => array("{$this->item_name}_id = ?", $item_id)));
        # return the deleted item
        return $item;
    }
}
