<?php
namespace core\db\models;

/**
 * Conversation Entity
 */
class conversation extends baseModel
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
     * @return array of conversation instances
     */
    public static function fetchAll($user_id) {
        return parent::all(array('conditions' => array('(user1 = ? OR user2 = ?)', $user_id, $user_id), 'order' => 'last_conversation_at desc'));
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
     * Fetch messages based on current conversation
     * @param string $sort The sort of result(default: desc)
     * @param integer $offset (optional) The offset# for pagination
     * @param integer $limit (optional) The limit# for pagination
     * @return array Of messages or NULL if no message found
     */
    public function fetch_messages($offset = -1, $limit = -1, $order = NULL) {
        # init args with a basic condition
        $args = array("conditions" => array("conversation_id  = ?", $this->conversation_id));
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
        # fetch all messages based on given arguments
        $m = message::all($args);
        # if no message found
        if(!count($m))
            # set messages instance to NULL
            $m = NULL;
        # return messages
        return $m;
    }
}
