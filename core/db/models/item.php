<?php
namespace core\db\models;
/**
 * General item entity
 */
abstract class item extends \ActiveRecord\Model
{
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
        #  after setting the table's name we go for parent contruction
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
        # check inputs
        foreach(array('title' => $title) as $key => $value)
        {
            # it should be string and not empty
            if(!\is_string($value) || empty($value))
                throw new \zinux\kernel\exceptions\invalideArgumentException("the $key has not been properly setted!");
        }
        # set the title
        $this->{"{$this->item_name}_title"} = $title;
        # set the body
        $this->{"{$this->item_name}_body"} = $body;
        # set the user name
        $this->owner_id = $owner_id;
        # set the parent id
        $this->parent_id = $parent_id;
        # generate an id
        $this->{"{$this->item_name}_id"} = \zinux\kernel\security\hash::Generate($parent_id.$title.$owner_id);
        try
        {
            # try to save it
            $this->save();
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
    }
    public function fetch($item_id)
    {
        return $this->find($item_id);
    }
    public function fetchItems($user_id, $parent_id)
    {
        return $this->find("all", array("conditions" => array("owner_id = ? AND parent_id = ?", $user_id, $parent_id)));
    }
}
