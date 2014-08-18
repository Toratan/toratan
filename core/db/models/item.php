<?php
namespace core\db\models;
/**
 * General item entity
 */
abstract class item extends baseModel
{
    static $validates_numericality_of = array(
            array('is_public', 'less_than_or_equal_to' => 1, 'greater_than_or_equal_to' => 0),
            array('is_trash', 'less_than_or_equal_to' => 1, 'greater_than_or_equal_to' => 0),
            array('is_archive', 'less_than_or_equal_to' => 1, 'greater_than_or_equal_to' => 0),
    );
    /**
     * @var array Before save callbacks
     */
    static $before_save = array('before_save_trim_properties');
    /**
     * @var array update notification table if necessary
     */
    static $after_save = array('after_save_update_notifications');
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
     * flag no-change
     */
    const NOCHANGE= self::NOOP;
    /**
     * It can pass as 3rd argument to delete() method
     * And it means the we demand that item get deleted for ever
     */
    const DELETE_PERIOD = self::NOOP;
    /**
     * It can pass as 3rd argument to delete() method
     * And it means the we demand that item get put in trash
     */
    const DELETE_PUT_TARSH = self::FLAG_SET;
    /**
     * It can pass as 3rd argument to delete() method
     * And it means the we demand that item get restored
     */
    const DELETE_RESTORE = self::FLAG_UNSET;
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
    /**
     * temporary container for internal uses
     * @var array
     */
    private $temporary_container = array();

    public function __construct(array $attributes = array(), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
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
        # after setting the table's name we go for parent contruction
        parent::__construct($attributes, $guard_attributes, $instantiating_via_find, $new_record);
        # unset the static table name
        self::$table_name = "";
    }
    /**
     * Get the current item's behavioral name
     * @return string
     */
    public function WhoAmI(){ return $this->item_name; }
    /**
     * Get the item's ID
     */
    public function getItemID() { return $this->{"{$this->WhoAmI()}_id"}; }
    /**
     * Get the item's TItle
     */
    public function getItemTitle() { return $this->{"{$this->WhoAmI()}_title"}; }
    /**
     * Get the item's body
     */
    public function getItemBody() { return $this->{"{$this->WhoAmI()}_body"}; }
    /**
     * Set the item's TItle
     */
    public function setItemTitle($title) { $this->{"{$this->WhoAmI()}_title"} = $title; }
    /**
     * Set the item's body
     */
    public function setItemBody($body) { $this->{"{$this->WhoAmI()}_body"} = $body; }
    /**
     * Creates a new item in { title | body } datastructure
     * @param string $title the item's title
     * @param string $body the item's body
     * @param string $parent_id the item's parent id
     * @param string $owner_id the item's owner
     * @throws \zinux\kernel\exceptions\invalidArgumentException if title not string or be empty
     * @throws \zinux\kernel\exceptions\invalidOperationException if duplication problem raise during saving item to db
     * @throws \core\db\models\Exception if any other exception raised that didn't match with previous excepions
     * @return item the create item
    */
    public function newItem(
            $title,
            $body,
            $parent_id,
            $owner_id) {
        # normali\core\db\exceptions\dbNotFoundExceptionzing the inputs
        $title = trim($title);
        $body = trim($body);
        # fetch the item's handler
        $item_class = get_called_class();
        # invoke an instance of item for item object
        $item = new $item_class;
        # invoke an instance of folder for parent
        $parent_item = new \core\db\models\folder();
        # find the parent item
        $parent = $parent_item->fetch($parent_id);
        # validate the parent existance?
        if(!$parent) 
            # apparently parent folder does not exists
            throw new \core\db\exceptions\dbNotFoundException("The parent folder not found!!");
        # validate  the owner id match with parent's owner id
        if($parent->owner_id && $parent->owner_id != $owner_id)
            throw new \zinux\kernel\exceptions\invalidOperationException("You don't have the write permission under directory# $parent_id");
        # set the title
       $item->{"{$this->item_name}_title"} = $title;
        # set the body
       $item->{"{$this->item_name}_body"} = $body;
        # set the user name
       $item->owner_id = $owner_id;
        # set the parent id
       $item->parent_id = $parent_id;
        # inherit parent is_public value
       $item->is_public = $parent->is_public;
        # inherit parent is_trash value
       $item->is_trash = $parent->is_trash;
        # inherit parent is_archive value
       $item->is_archive = $parent->is_archive;
        #save it
       $item->save();
       # return the created item
       return $item;
    }
    /**
     * normalize the conditions and options to use in \ActiveRecord::Model::find()
     * @param array $conditions the find conditons
     * @param array $options the other options this also can have conditons in it
     * @return array the genaral options
     * @throws \zinux\kernel\exceptions\invalidArgumentException if $options has "condition" and it does not have the conditons string
     */
    protected function normalize_conditions_options_ops(
            $conditions = array(),
            $options = array()) {
        # normalize the array
        if(!$conditions) $conditions = array();
        if(!$options) $options = array();
        # normalize the conditions array
        if(!isset($conditions["conditions"]))
            $conditions = array("conditions" => $conditions);
        # if we have conditions in $options
        # then we have merging conflict problem
        if(isset($options["conditions"]) && count($options["conditions"])) {
            # if the option has invalid conditions format
            if(!is_string($options["conditions"][0]))
                # flag the error
                throw new \zinux\kernel\exceptions\invalidArgumentException("invalid \$options format");
            # merge the $options' conditional string with genuine $conditions array
            $conditions["conditions"][0] .= " AND (".array_shift($options["conditions"]).")";
            # fetch the othe condition arguments, if any exists
            while(count($options["conditions"]))
                $conditions["conditions"][] = array_shift($options["conditions"]);
            # replace the new generated conditions
            $options["conditions"] = $conditions["conditions"];
        }
        else
        {
            # if no conditions presence at $options, just make a room for it
            $options ["conditions"] = $conditions["conditions"];
        }
        # return the re-configured $options array
        return $options;
    }
    /**
     * Fetches a single item from database
     * @param string $item_id item's id
     * @param string $owner_id item's owner id
     * @param array $options see \ActiveRecord\Model::find()
     * @return item
     */
    public function fetch(
            $item_id,
            $owner_id = NULL,
            $options = array()) {
        $cond = array("conditions" => array("{$this->item_name}_id = ?", $item_id));
        if($owner_id !== NULL)
            $cond = array("conditions" => array("{$this->item_name}_id = ? AND (owner_id = ? OR is_public = 1)", $item_id, $owner_id));
        # normalize the conditions with any passed options
        $options = $this->normalize_conditions_options_ops($cond, $options);
        $item = $this->find($item_id, $options);
        if(!$item)
            throw new \core\db\exceptions\dbNotFoundException("$this->item_name with ID# `$item_id` not found or you don't have the access premission!");
        # update temporary shareing status
        $item->storeStatusBits();
        return $item;
    }
    /**
     * Fetches all sub-items under a parent directory with an owner
     * @param string $owner_id items' owner id
     * @param string $parent_id items' parent id, pass '<b>NULL </b>' to select in all parrent pattern
     * @param boolean $is_public should it be public or not, pass '<b>item::WHATEVER</b>' to don't matter
     * @param boolean $is_trash should it be trashed or not, pass '<b>item::WHATEVER</b>' to don't matter
     * @param boolean $is_archive should it be archive or not, pass '<b>item::WHATEVER</b>' to don't matter
     * @param array $options see \ActiveRecord\Model::find()
     * @return array of items
     */
    public function fetchItems(
            $owner_id,
            $parent_id = NULL,
            $is_public = self::WHATEVER,
            $is_trash = self::WHATEVER,
            $is_archive = self::WHATEVER,
            $options = array()) {
        # general conditions
        $item_cond = "(owner_id = ?) ";
        $cond = array($item_cond, $owner_id);
        if($parent_id !== NULL) {
            $cond[0] .= "AND parent_id = ? ";
            $cond[] = $parent_id;
        }
        foreach(
                array("is_public" => $is_public, "is_trash"=>$is_trash, "is_archive" => $is_archive)
                as $name => $value) {
            # if is public revoked
            if($value != self::WHATEVER) {
                # flag it
                $cond[0] .= "AND $name =  ? ";
                $cond[] = $value;
            }
        }
        # normalize the conditions with any passed options
        $options = $this->normalize_conditions_options_ops($cond, $options);
        # returns all items with given owner and parent id
        $items = $this->find("all", $options);
        # validate if null?
        if(!$items) return $items;
        # foreach fetched item
        foreach($items as $item) {
            # update items old_instance properties
            $item->storeStatusBits();
        }
        # return fetched items
        return $items;
    }
    /**
     * Edits an item
     * @param string $item_id the item's id
     * @param string $owner_id the item's owner id
     * @param string $title string the item's title
     * @param string $body the item's body
     * @param boolean $is_public should it be public or not, pass '<b>item::NOCHANGE</b>' to don't chnage
     * @param boolean $is_trash should it be trashed or not, pass '<b>item::NOCHANGE</b>' to don't chnage
     * @param boolean $is_archive should it be archived or not, pass '<b>item::NOCHANGE</b>' to don't chnage
     * @return item the edited item
     * @throws \core\db\exceptions\dbNotFoundException if the item not found
     */
    public function edit(
            $item_id,
            $owner_id,
            $title,
            $body,
            $is_public = self::NOCHANGE,
            $is_trash = self::NOCHANGE,
            $is_archive = self::NOCHANGE) {
        # fetch the item
        $item = $this->fetch($item_id, $owner_id);
        # set the title
        $item->{"{$this->item_name}_title"} = $title;
        # set the body
        $item->{"{$this->item_name}_body"} = $body;
        # modify the publicity of the item if necessary
        if($is_public!=self::NOCHANGE)
            $item->is_public = $is_public;
        # modify the trash flag of the item if necessary
        if($is_trash!=self::NOCHANGE)
            $item->is_trash = $is_trash;
        # modify the archive flag of the item if necessary
        if($is_archive!=self::NOCHANGE)
            $item->is_archive = $is_archive;
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
    public function delete(
            array $items_id,
            $owner_id,
            $TRASH_OPS = self::DELETE_PUT_TARSH) {
        # normalize/escape the items id
        $items_id = self::escape_in_query($items_id);
        # invoke a sql builder
        $builder = new \ActiveRecord\SQLBuilder(self::connection(), self::table_name());
        # if we should flag it as trash
        switch($TRASH_OPS) {
            case self::DELETE_RESTORE:
            case self::DELETE_PUT_TARSH:
                # so be it
                $builder->update(array("is_trash" => $TRASH_OPS));
                break;
            case self::DELETE_PERIOD:
                # detele permanent
                $builder->delete();
                break;
            default:
                throw new \zinux\kernel\exceptions\invalidArgumentException("undefined ops demand!");
        }
        # construct the WHERE clause
        $builder->where("owner_id = ? AND {$this->WhoAmI()}_id IN ($items_id)", $owner_id);
        # execute the query
        self::query($builder->to_s(), $builder->bind_values());
    }
    /**
     * Fetches all trash items that the owner has
     * @param string $owner_id
     * @return array the trash items
     */
    public function fetchTrashes($owner_id, $options = array()) {
        return $this->fetchItems($owner_id, NULL, self::WHATEVER, self::FLAG_SET, self::WHATEVER, $options);
    }
    /**
     * Arhives/De-archives an item
     * @param string $item_id the item's ID
     * @param string $owner_id the item's owner's ID
     * @param integer $ARCHIVE_STATUS valid input for this are <b>self::FLAG_SET</b>, <b>self::FLAG_UNSET</b>
     * @return item the modified item
     * @throws \zinux\kernel\exceptions\invalidOperationException if $ARCHIVE_STATUS is not valid
     */
    public function archive(
            array $items_id,
            $owner_id,
            $ARCHIVE_STATUS = self::FLAG_SET) {
        # normalize/escape the items id
        $items_id = self::escape_in_query($items_id);
        # invoke a sql builder
        $builder = new \ActiveRecord\SQLBuilder(self::connection(), self::table_name());
        # validate the archive status
        switch($ARCHIVE_STATUS) {
            case self::FLAG_SET:
            case self::FLAG_UNSET:
                $builder->update(array("is_archive" => $ARCHIVE_STATUS));
                break;
            default:
                throw new \zinux\kernel\exceptions\invalidOperationException;
        }
        # construct the WHERE clause
        $builder->where("owner_id = ? AND {$this->WhoAmI()}_id IN ($items_id)", $owner_id);
        # execute the query
        self::query($builder->to_s(), $builder->bind_values());
    }
    /**
     * Arhives/De-shares an item
     * @param string $item_id the item's ID
     * @param string $owner_id the item's owner's ID
     * @param integer $SHARE_STATUS valid input for this are <b>self::FLAG_SET</b>, <b>self::FLAG_UNSET</b>
     * @return item the modified item
     * @throws \zinux\kernel\exceptions\invalidOperationException if $SHARE_STATUS is not valid
     */
    public function share(
            array $items_id,
            $owner_id,
            $SHARE_STATUS = self::FLAG_SET) {
        # normalize/escape the items id
        $items_id = self::escape_in_query($items_id);
        # invoke a sql builder
        $builder = new \ActiveRecord\SQLBuilder(self::connection(), self::table_name());
        # validate the share status
        switch($SHARE_STATUS) {
            case self::FLAG_SET:
            case self::FLAG_UNSET:
                $builder->update(array("is_public" => $SHARE_STATUS));
                break;
            default:
                throw new \zinux\kernel\exceptions\invalidOperationException;
        }
        # construct the WHERE clause
        $builder->where("owner_id = ? AND {$this->WhoAmI()}_id IN ($items_id)", $owner_id);
        # execute the query
        self::query($builder->to_s(), $builder->bind_values());
    }
    /**
     * Fetches all archived items that the owner has
     * @param string $owner_id
     * @return array the archive items
     */
    public function fetchArchives($owner_id, $options = array()) {
        return $this->fetchItems($owner_id, NULL, self::WHATEVER, self::FLAG_UNSET, self::FLAG_SET, $options);
    }
    /**
     * moves an item
     * @param array $item_id the items' id collection
     * @param string $owner_id the item's owner id
     * @param string $new_parent_id the new parent id
     */
    public function move(array $item_ids, $owner_id, $new_parent_id) {
        $builder = new \ActiveRecord\SQLBuilder(self::connection(), self::table_name());
        $builder->update(array("parent_id" => $new_parent_id))->where("owner_id = ? AND {$this->WhoAmI()}_id IN({$this->escape_in_query($item_ids)})", $owner_id);
        $this->query($builder->to_s(), $builder->bind_values());
    }
    /**
     * Fetches all shared items that the owner has
     * @param string $owner_id
     * @return array the shared items
     */
    public function fetchShared($owner_id, $options = array()) {
        return $this->fetchItems($owner_id, NULL, self::FLAG_SET, self::FLAG_UNSET, self::WHATEVER, $options);
    }
    /**
     * Fetches verbal route to toot from an item
     * @param string $item_id the item's ID
     * @param string $owner_id the item's owner's ID
     * @return array An array of item which shows the route to the item
     */
    public function fetchRouteToRoot($item_id, $owner_id) {
        $route = array();
        if(!$item_id)
            goto __RETURN;
        $item = $this->fetch($item_id, $owner_id);
        array_push($route, $item);
        while($item->{"parent_id"}!=0) {
            $item = $this->fetch($item->parent_id, $owner_id);
            $item->readonly(TRUE);
            array_push($route, $item);
        }
__RETURN:
        $root = $this->fetch(0);
        $root->readonly(TRUE);
        $root->setItemTitle(ucfirst(strtolower($root->getItemTitle())));
        array_push($route, $root);
        return array_reverse($route);
    }
    /**
     * Disables auto notification ops.
     */
    public function disableAutoNotification(){ unset($this->temporary_container["BSS"]); }
    /**
     * Updates old instance of current user
     */
    protected function storeStatusBits() {
        # BSB: Binary Status State
        $this->temporary_container["BSS"]  = $this->getStatusBits();
    }
    /**
     * fetch stored status bits; if no status bit assigned {0} will be returned
     * @return binary
     */
    protected function fetchStatusBits() {
        if(isset($this->temporary_container["BSS"]))
            return $this->temporary_container["BSS"];
        return 0b0000;
    }
    /**
     * Get binary status state of current item<br />
     * The format is: 0b{public}{trash}{zero}{validateBit}
     * @return binary
     */
    protected function getStatusBits() {
        $flag = 0b0001;
        # is_public never selected
        if(isset($this->is_public) && $this->is_public)
            $flag = $flag | 0b1001;
        # is_trash never selected
        if(isset($this->is_trash) && $this->is_trash)
            $flag = $flag | 0b0101;
        return $flag;
    }
    /**
     * General validator
     */
    public function validate() {
        # validate the item's title existance
        if(!isset($this->{"{$this->item_name}_title"}) || !\strlen($this->{"{$this->item_name}_title"}))
            $this->errors->add("{$this->item_name}'s title", "cannot be blank.");
    }
    /**
     * Trims properties just before they get saved
     */
    public function before_save_trim_properties() {
        $this->{"{$this->item_name}_title"} = trim($this->{"{$this->item_name}_title"});
        $this->{"{$this->item_name}_body"} = trim($this->{"{$this->item_name}_body"});
    }
    /**
     * smart checks points for notification outputs flow
     */
    public function after_save_update_notifications() {
        # if any status bit has been stored
        if(($obss = $this->fetchStatusBits())) {
            $cbss = $this->getStatusBits();
            # nothing notifiable has been changed
            if($obss === $cbss) return;
            # validate the publicity
            # if publicity has been changed
            if(($obss & 0b1000) !== ($cbss & 0b1000)) {
                if($this->is_public)
                    \core\db\models\notification::put($this->owner_id, $this->{"{$this->item_name}_id"}, $this->item_name, \core\db\models\notification::NOTIF_FLAG_SHARE);
                else
                    \core\db\models\notification::deleteNotification($this->owner_id, $this->{"{$this->item_name}_id"}, $this->item_name);
                /* WE DON'T GO FOR RECURSIVE SHARING FOLDER SHARING */
                if(false && $this->item_name == "folder")
                    # update the sub-items sharing status
                    \core\db\models\sharing_queue::add_queue($this);
            }
            # if trash state has been changed and this is a public item
            if((($obss & 0b0100) !== ($cbss & 0b0100)) && $this->is_public) {
                if($this->is_trash)
                    \core\db\models\notification::visibleNotification($this->owner_id, $this->{"{$this->item_name}_id"}, $this->item_name, 0);
                else
                    \core\db\models\notification::visibleNotification($this->owner_id, $this->{"{$this->item_name}_id"}, $this->item_name, 1);
            }
        }
    }
}
