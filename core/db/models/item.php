<?php
namespace core\db\models;
/**
 * General item entity
 */
abstract class item extends abstractModel
{
    static $belongs_to = array(
            array("user", 'foreign_key' => 'owner_id')
    );
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
     * temporary container for internal uses
     * @var array
     */
    private $temporary_container = array();
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
     * Fetches a single item from database
     * @param string $item_id item's id
     * @param string $owner_id item's owner id
     * @param array $options see \ActiveRecord\Model::find()
     * @return item
     */
    public function fetch(
            $item_id,
            $owner_id = self::WHATEVER,
            $options = array()) {
        $cond = array("conditions" => array("{$this->item_name}_id = ?", $item_id));
        if($owner_id != self::WHATEVER)
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
            $owner_id = self::WHATEVER,
            $parent_id = self::WHATEVER,
            $is_public = self::WHATEVER,
            $is_trash = self::WHATEVER,
            $is_archive = self::WHATEVER,
            $options = array()) {
        # general conditions
        $cond = array("1 ");
        # if owner item specified?
        if($owner_id != self::WHATEVER) {
            $cond[0] .= "AND owner_id = ? ";
            $cond[] = $owner_id;
        }
        if($parent_id != self::WHATEVER) {
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
     * @param string $parent_id the item's new parent ID, pass '<b>item::NOCHANGE</b>' to don't chnage
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
            $parent_id = self::NOCHANGE,
            $is_public = self::NOCHANGE,
            $is_trash = self::NOCHANGE,
            $is_archive = self::NOCHANGE) {
        # fetch the item
        $item = $this->fetch($item_id, $owner_id);
        # set the title
        $item->{"{$this->item_name}_title"} = $title;
        # set the body
        $item->{"{$this->item_name}_body"} = $body;
        # modify the parent ID of the item if necessary
        if($parent_id!=self::NOCHANGE)
            $item->parent_id = $parent_id;
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
     * Deletes/Restores a collection of items
     * @param array $items_id the items' ID
     * @param string $owner_id the items' owner's ID
     * @param integet $TRASH_OPS can be one of <b>item::DELETE_RESTORE</b>, <b>item::DELETE_PUT_TARSH</b>, <b>item::DELETE_PERIOD</b>
     * @throws \zinux\kernel\exceptions\invalidOperationException if $TRASH_OPS is not valid
     * @return integer Number of rows affected
     */
    public function delete_item(
            array $items_id,
            $owner_id,
            $TRASH_OPS = self::DELETE_PUT_TARSH) {
        # return on empty query
        if(!$items_id || !count($items_id)) return;
        # normalize/escape the items id
        $items_id = self::escape_in_query($items_id);
        # invoke a sql builder
        $builder = self::getSQLBuilder();
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
        $ret = self::query($builder->to_s(), $builder->bind_values());
        # return the affected rows
        return $ret->rowCount();
    }
    /**
     * Arhives/De-archives a collection of items
     * @param array $items_id the items' ID
     * @param string $owner_id the items' owner's ID
     * @param integer $ARCHIVE_STATUS valid input for this are <b>self::FLAG_SET</b>, <b>self::FLAG_UNSET</b>
     * @throws \zinux\kernel\exceptions\invalidOperationException if $ARCHIVE_STATUS is not valid
     * @return integer Number of rows affected
     */
    public function archive(
            array $items_id,
            $owner_id,
            $ARCHIVE_STATUS = self::FLAG_SET) {
        # return on empty query
        if(!$items_id || !count($items_id)) return;
        # normalize/escape the items id
        $items_id = self::escape_in_query($items_id);
        # invoke a sql builder
        $builder = self::getSQLBuilder();
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
        $ret = self::query($builder->to_s(), $builder->bind_values());
        # return the affected rows
        return $ret->rowCount();
    }
    /**
     * Arhives/De-shares a collection of items
     * @param array $items_id the items' ID
     * @param string $owner_id the items' owner's ID
     * @param integer $SHARE_STATUS valid input for this are <b>self::FLAG_SET</b>, <b>self::FLAG_UNSET</b>
     * @throws \zinux\kernel\exceptions\invalidOperationException if $SHARE_STATUS is not valid
     * @return integer Number of rows affected
     */
    public function share(
            array $items_id,
            $owner_id,
            $SHARE_STATUS = self::FLAG_SET) {
        # return on empty query
        if(!$items_id || !count($items_id)) return;
        # normalize/escape the items id
        $items_id = self::escape_in_query($items_id);
        # invoke a sql builder
        $builder = self::getSQLBuilder();
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
        $ret = self::query($builder->to_s(), $builder->bind_values());
        # return the affected rows
        return $ret->rowCount();
    }
    /**
     * moves an item
     * @param array $item_id the items' id collection
     * @param string $owner_id the item's owner id
     * @param string $new_parent_id the new parent id
     * @return integer Number of rows affected
     */
    public function move(array $items_id, $owner_id, $new_parent_id) {
        # return on empty query
        if(!$items_id || !count($items_id)) return;
        # invoke a sql builder
        $builder = self::getSQLBuilder();
        # build the query
        $builder->update(array("parent_id" => $new_parent_id))->where("owner_id = ? AND {$this->WhoAmI()}_id IN({$this->escape_in_query($items_id)})", $owner_id);
        # execute the query
        $ret = self::query($builder->to_s(), $builder->bind_values());
        # return the affected rows
        return $ret->rowCount();
    }
    /**
     * updates last visit column of gived items' ID
     * @param array $item_id the items' id collection
     * @param string $owner_id the item's owner id
     * @return integer Number of rows affected
     */
    public function update_last_visit_at(array $items_id, $owner_id) {
        # return on empty query
        if(!$items_id || !count($items_id)) return;
        # invoke a sql builder
        $builder = self::getSQLBuilder();
        # build the query
        $builder->update(array("last_visit_at" => (new \ActiveRecord\DateTime)->__toString()))->where("owner_id = ? AND {$this->WhoAmI()}_id IN({$this->escape_in_query($items_id)})", $owner_id);
        # execute the query
        $ret = self::query($builder->to_s(), $builder->bind_values());
        # return the affected rows
        return $ret->rowCount();
    }
    /**
     * Fetches all trash items that the owner has
     * @param string $owner_id
     * @return array the trash items
     */
    public function fetchTrashes($owner_id, $options = array()) {
        return $this->fetchItems($owner_id, self::WHATEVER, self::WHATEVER, self::FLAG_SET, self::WHATEVER, $options);
    }
    /**
     * Fetches all archived items that the owner has
     * @param string $owner_id
     * @return array the archive items
     */
    public function fetchArchives($owner_id, $options = array()) {
        return $this->fetchItems($owner_id, self::WHATEVER, self::WHATEVER, self::FLAG_UNSET, self::FLAG_SET, $options);
    }
    /**
     * Fetches all shared items that the owner has
     * @param string $owner_id
     * @return array the shared items
     */
    public function fetchShared($owner_id, $options = array()) {
        return $this->fetchItems($owner_id, self::WHATEVER, self::FLAG_SET, self::FLAG_UNSET, self::WHATEVER, $options);
    }
    /**
     * fetches the items base on their popularity
     * @param string $owner_id
     * @param integer $offset The offset of query
     * @param integer $limit The limit for query
     * @param boolean $is_public should it be public or not, pass '<b>item::WHATEVER</b>' to don't matter
     * @param boolean $is_trash should it be trashed or not, pass '<b>item::WHATEVER</b>' to don't matter
     * @param boolean $is_archive should it be archive or not, pass '<b>item::WHATEVER</b>' to don't matter
     * @param boolean $reverse (default: false) if it assigned to TRUE the the result would be the LEAST popular item
     * @param array $options see \ActiveRecord\Model::find()
     * @return array of fetched items
     */
    public function fetchPopular(
            $owner_id = self::WHATEVER,
            $offset = 0,
            $limit = 5,
            $is_public = self::WHATEVER,
            $is_trash = self::WHATEVER,
            $is_archive = self::WHATEVER,
            $reverse = 0,
            $options = array()) {
        $options["offset"] = $offset;
        $options["limit"] = $limit;
        $options["order"] = "popularity " . ($reverse ? "ASC" : "DESC").", created_at DESC";
        return $this->fetchItems($owner_id, self::WHATEVER, $is_public, $is_trash, $is_archive, $options);
    }
    /**
     * Increases the item's popularity
     * @param boolean $auto_save (default: true) Shoud the method attempt to autosave after the assignment?
     * @return The after increase popularity rate
     */
    public function increase_popularity($auto_save = 1){
        $this->popularity += 0.1;
        if($auto_save)
            $this->save();
        return $this->popularity;
    }
    /**
     * Decreases the items's popularity
     * @param boolean $auto_save (default: true) Shoud the method attempt to autosave after the assignment?
     * @return The after decrease popularity rate
     */
    public function decrease_popularity($auto_save = 1){
        $this->popularity -= 0.1;
        if($auto_save)
            $this->save();
        return $this->popularity;
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
     * fetch stored status bits; if no status bit assigned {0} will be returned
     * @return binary
     */
    public function fetchStatusBits() {
        if(isset($this->temporary_container["BSS"]))
            return $this->temporary_container["BSS"];
        return 0b0000;
    }
    /**
     * Updates old instance of current user
     */
    protected function storeStatusBits() {
        # BSB: Binary Status State
        $this->temporary_container["BSS"]  = $this->getStatusBits();
    }
    /**
     * Get binary status state of current item<br />
     * The format is: 0b{public}{trash}{zero}{validateBit}
     * @return binary
     */
    protected function getStatusBits() {
        return \core\db\vendors\itemStatus::encode($this);
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
            # compute the difference between two status
            $dbss = \core\db\vendors\itemStatus::decode($obss)->diff(\core\db\vendors\itemStatus::decode($cbss));
            # validate the publicity
            # if publicity has been changed
            if($dbss->is_public) {
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
            if($dbss->is_trash && $this->is_public) {
                if($this->is_trash)
                    \core\db\models\notification::visibleNotification($this->owner_id, $this->{"{$this->item_name}_id"}, $this->item_name, 0);
                else
                    \core\db\models\notification::visibleNotification($this->owner_id, $this->{"{$this->item_name}_id"}, $this->item_name, 1);
            }
        }
    }
}