<?php
namespace core\db\models;

/**
 * Conversation Entity
 */
class conversation extends communicationModel
{
    static $belongs_to = array(
      array('profile', 'readonly' => true ,'select' => 'user_id, first_name, last_name')
    );
    /**
     * Opens a conversation between users; Or create a new one if not exists
     * @param $user_id1 User1's ID
     * @param $user_id2 User2's ID
     * @param boolean $auto_create If no conversation exists, should create a new conversation?
     * @return conversation
     */
    public static function open($user_id1, $user_id2, $auto_create = 1) {
        $c = self::fetch($user_id1, $user_id2);
        if(!$c && $auto_create) {
            $c = new self;
            $c->user1 = $user_id1;
            $c->user2 = $user_id2;
            $c->save();
        }
        # if mark as below?
        switch($c->marked_as){
            case self::MARKED_AS_SPAM:
                throw new \zinux\kernel\exceptions\accessDeniedException("It is not possible to open an spammed conversation #{$c->conversation_id}");
        }
        return $c;
    }
    /**
     * Check if any coversation exists between users
     * @param $user_id1 User1's ID
     * @param $user_id2 User2's ID
     * @return boolean TRUE if exist; otherwise FALSE
     */
    public static function exists($user_id1, $user_id2) {
        if(self::fetch($user_id1, $user_id2)) return true;
        return false;
    }
    /**
     * Fetches a conversation between users
     * @param $user_id1 User1's ID
     * @param $user_id2 User2's ID
     * @return conversation if no conversation exists, returns FALSE;
     */
    public static function fetch($user_id1, $user_id2) {
        return parent::find(array('conditions' => array('(user1 = ? AND user2 = ?) OR (user2 = ? AND user1 = ?)', $user_id1, $user_id2, $user_id1, $user_id2)));
    }
    /**
     * Fetch a user's all conversation
     * @param $user_id The user's ID
     * @param integer $offset (optional) The offset# for pagination
     * @param integer $limit (optional) The limit# for pagination
     * @return array of conversation instances
     */
    public static function fetchAll($user_id, $offset = -1, $limit = -1, $non_deleted = 1, $marked_as = self::MARKED_AS_NORMAL) {
        # init args with a basic condition
        $args = array('order' => 'last_conversation_at DESC');
        # init conditions
        $cond = array('(user1 = ? OR user2 = ?)', $user_id, $user_id);
        # if fetching non-deleted conv.?
        if($non_deleted) {
            # update the conditions
            $cond[0] .= " AND (deleted_id IS NULL OR deleted_id != ?)";
            $cond[] = $user_id;
        }
        # if marked as is defined?
        if(is_integer($marked_as)) {
            # validate the input
            switch($marked_as) {
                case self::MARKED_AS_NORMAL:
                case self::MARKED_AS_SPAM:
                    break;
                default:
                    throw new \zinux\kernel\exceptions\invalidArgumentException("Invalid \$marked_as value : `$marked_as`");
            }
            # update the conditions
            $cond[0] .= " AND marked_as = ?";
            $cond[] = $marked_as;
        }
        # if any positive offset arg passed
        if($offset >= 0)
            $args["offset"] = $offset;
        # if any positive limit arg passed
        if($limit >= 0)
            $args["limit"] = $limit;
        elseif($offset >= 0)
            throw new \zinux\kernel\exceptions\invalidOperationException("When \$offset is set expecting \$limit to be set too; but didn't!");
        # inject the conditions into args
        $args["conditions"] = $cond;
        # fetch all
        return parent::all($args);
    }
    /**
     * Counts user's all conversation
     * @param $user_id The user's ID for whom is fetching for
     * @return integer # of user's conversations
     */
    public static function countAll($user_id, $non_deleted = self::FLAG_SET, $is_read = self::WHATEVER, $maked_as = self::WHATEVER) {
        $count = 0;
        foreach(array("user1", "user2") as $user_side) {
            $cond = array("$user_side  = ?", $user_id);
            if($non_deleted == self::FLAG_SET) {
                $cond[0] .= " AND (deleted_id IS NULL OR deleted_id != ?)";
                $cond[] = $user_id;
            }
            foreach(array("has_read_$user_side" => $is_read, "marked_as" => $maked_as) as $column => $value) {
                if($value != self::WHATEVER) {
                    $cond[0] .= " AND $column = ?";
                    $cond[] = $value;
                }
            }
            $count += parent::count(array('conditions' => $cond));
        }
        return $count;
    }
    /**
     * Updates the current conversation's date
     * @param \ActiveRecord\DateTime $dt Target datetime
     * @return conversation $this
     */
    public function update2date(\ActiveRecord\DateTime $dt){
        $this->last_conversation_at = $dt;
        $this->save();
        return $this;
    }
    /**
     * Marks current conversation as read
     * @param $current_user The current user ID that is marking the conversation as read
     */
    public function  marked_as_read($current_user) {
        if($this->user1 != $current_user && $this->user2 != $current_user)
            throw new \zinux\kernel\exceptions\invalidOperationException("The user#`$current_user` is not part of conversation#`$this->conversation_id`.");
        message::update_all(array("set" => array("is_read" => 1), "conditions" => array("conversation_id = ? AND receiver_id = ? AND is_read <> 1", $this->conversation_id, $current_user)));
        $is_read = "has_read_".($this->user1 == $current_user ? "user1" : "user2");
        $this->$is_read = 1;
        $this->save();
    }
    /**
     * Checks if current conversation is seen by current user?
     * @param $current_user The current user ID
     * @return boolean if this conversation is seen by current user returns TRUE; otherwise FALSE
     */
    public function is_conversation_seen($current_user) {
        if($this->user1 != $current_user && $this->user2 != $current_user)
            throw new \zinux\kernel\exceptions\invalidOperationException("The user#`$current_user` is not part of conversation#`$this->conversation_id`.");
        $is_read = "has_read_".($this->user1 == $current_user ? "user1" : "user2");
        return (bool)($this->$is_read || message::last($this->user1, $this->user2, $current_user)->sender_id == $current_user);
    }
    /**
     * Fetch messages based on current conversation
     * @param integer $offset (optional) The offset# for pagination
     * @param integer $limit (optional) The limit# for pagination
     * @param string $order The sort of result(default: `created_at` DESC)
     * @return array Of messages or NULL if no message found
     */
    public function fetch_messages($user_id, $offset = -1, $limit = -1, $order = NULL, $non_deleted = 1) {
        # init args with a basic condition
        $args = array("conditions" => array("conversation_id  = ?", $this->conversation_id), "order" => "`created_at` DESC");
        # if any positive offset arg passed
        if($offset >= 0)
            $args["offset"] = $offset;
        # if any positive limit arg passed
        if($limit >= 0)
            $args["limit"] = $limit;
        elseif($offset >= 0)
            throw new \zinux\kernel\exceptions\invalidOperationException("When \$offset is set expecting \$limit to be set too; but didn't!");
        # any specific order passed
        if($order)
            $args["order"] = $order;
        # if we are fetching messages which are not deleted by $user_id yet?
        if($non_deleted) {
            $args["conditions"][0] .= " AND (deleted_id IS NULL OR deleted_id NOT IN (?))";
            $args["conditions"][] = $user_id;
        }
        # fetch all messages based on given arguments
        $m = message::all($args);
        # if no message found
        if(!count($m))
            # set messages instance to NULL
            $m = NULL;
        # return messages
        return $m;
    }
    /**
     * Delete current conversation at point of view a user id, if both users of the conversation
     * happen to delete the same conversation, the conversation will actually get deleted from database, but if only one of them
     * delete the conversation the conversation will be invisible to that user when using `conversation::fetchAll()`
     * @param $user_id
     */
    public function deleteConversation($user_id) {
        if($this->user1 != $user_id && $this->user2 != $user_id)
            throw new \zinux\kernel\exceptions\invalidOperationException("The user#`$user_id` is not part of conversation#`$this->conversation_id`.");
        if(!$this->deleted_id){
            $this->deleted_id = $user_id;
            $this->save();
        }
        else {
            $this->delete();
            $this->readonly();
        }
    }
}
