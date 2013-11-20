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
            array('is_archive', 'less_than_or_equal_to' => 1, 'greater_than_or_equal_to' => 0),
    );
    /**
     * In certain operations that we want to do NOOP on some
     * prespective of item we pass this
     */
    const NOOP = -1;
    /**
     * the set flag value
     */
    const FLAG_SET = 1;
    /**
     * the unset flag value
     */
    const FLAG_UNSET = 0;
    /**
     * flag whatever
     */
    const WHATEVER = self::NOOP;
    /**
     * It can pass as 3rd argument to delete() method 
     * And it means the we demand that item get deleted for ever
     */
    const DELETE_PERIOD = self::FLAG_UNSET;
    /**
     * It can pass as 3rd argument to delete() method 
     * And it means the we demand that item get put in trash
     */
    const DELETE_PUT_TARSH = self::FLAG_SET;
    /**
     * It can pass as 3rd argument to delete() method 
     * And it means the we demand that item get restored
     */
    const DELETE_RESTORE = self::NOOP;
    /**
     *  genaral presence validation container
     */
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
     * @param boolean $is_public should it be public or not, pass '<b>item::NOOP</b>' to don't change
     * @param boolean $is_trash should it be trashed or not, pass '<b>item::NOOP</b>' to don't chnage
     * @return array of items
     */
    public function fetchItems($parent_id, $owner_id, $is_public = self::NOOP , $is_trash = self::NOOP, $is_archive = self::NOOP)
    {
        # general conditions
        $item_cond = "owner_id = ? AND parent_id = ? ";
        $cond = array($item_cond, $owner_id, $parent_id);

        foreach(
                array("is_public" => $is_public, "is_trash"=>$is_trash, "is_archive" => $is_archive) 
                as $name => $value)
        {
            # if is public revoked
            if($value>-1 && $value<2)
            {
                # flag it
                $cond[0] .= "AND $name =  ?";
                $cond[] = $value;
            }
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
     * @param boolean $is_public should it be public or not, pass '<b>item::NOOP</b>' to don't chnage
     * @param boolean $is_trash should it be trashed or not, pass '<b>item::NOOP</b>' to don't chnage
     * @throws \core\db\exceptions\dbNotFound if the item not found
     */
    public function edit($item_id, $owner_id, $title, $body, $is_public = self::NOOP, $is_trash = self::NOOP,  $is_archive = self::NOOP)
    {
        # fetch the item
        $item = $this->fetch($item_id, $owner_id);
        # set the title
        $item->{"{$this->item_name}_title"} = $title;
        # set the body
        $item->{"{$this->item_name}_body"} = $body;
        # modify the publicity of the item if necessary
        if($is_public!=self::NOOP)
            $item->is_public = $is_public;
        # modify the trash flag of the item if necessary
        if($is_trash!=self::NOOP)
            $item->is_trash = $is_trash;
        # save the item
        $item->save();
        # return the edited item
        return $item;
    }
    /**
     * Deletes/Restores an item
     * @param string $item_id the item's ID
     * @param string $owner_id the item's owner's ID
     * @param integet $TRASH_OPS can be one of <b>item::DELETE_RESTORE</b>, <b>item::DELETE_PUT_TARSH</b>, <b>item::DELETE_PERIOD</b>
     * @return item the deleted item
     */
    public function delete($item_id, $owner_id, $TRASH_OPS = self::DELETE_PUT_TARSH)
    {
        # fetch the item
        $item = $this->fetch($item_id, $owner_id);
        # if we should flag it as trash
        switch($TRASH_OPS)
        {
            case self::DELETE_PUT_TARSH:
                # so be it
                $item->is_trash = 1;
                $item->save();
                break;
            case self::DELETE_PERIOD:
                # detele permanent
                $item->delete_all(array("conditions" => array("{$this->item_name}_id = ?", $item_id)));
                break;
            case self::DELETE_RESTORE:
                # restore the item
                $item->is_trash = 0;
                $item->save();
                break;
            default:
                throw new \zinux\kernel\exceptions\invalideArgumentException("undefined ops demand!");
        }
        # return the deleted item
        return $item;
    }
    /**
     * Fetches all trash items that the owner has
     * @param string $owner_id
     * @return array the trash items
     */
    public function fetchTrashes($owner_id)
    {
        return $this->find("all", array("conditions" => array("owner_id = ? AND is_trash = ?", $owner_id, 1)));
    }
    /**
     * Arhives/De-archives an item
     * @param string $item_id the item's ID
     * @param string $owner_id the item's owner's ID
     * @param integer $ARCHIVE_STATUS valid input for this are <b>self::FLAG_SET</b>, <b>self::FLAG_UNSET</b>
     * @return item the modified item
     * @throws \zinux\kernel\exceptions\invalideOperationException if $ARCHIVE_STATUS is not valid
     */
    public function archive($item_id, $owner_id, $ARCHIVE_STATUS = self::FLAG_SET)
    {
        # fetch the item
        $item = $this->fetch($item_id, $owner_id);
        # validate the archive status
        switch($ARCHIVE_STATUS)
        {
            case self::FLAG_SET:
            case self::FLAG_UNSET:
                $item->is_archive = $ARCHIVE_STATUS;
                $item->save();
                break;
            default:
                throw new \zinux\kernel\exceptions\invalideOperationException;
        }
        # return the item
        return $item;
    }
    /**
     * Fetches all archived items that the owner has
     * @param string $owner_id
     * @return array the archive items
     */
    public function fetchArchives($owner_id)
    {
        return $this->find("all", array("conditions" => array("owner_id = ? AND is_archive = ? AND is_trash <> 1", $owner_id, 1)));
    }
}
