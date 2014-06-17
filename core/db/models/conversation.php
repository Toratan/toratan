<?php
namespace core\db\models;

/**
 * Conversation Entity
 */
class conversation extends baseModel
{
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
     * Updates the current conversation's date
     * @param \ActiveRecord\DateTime $dt Target datetime
     * @return conversation $this
     */
    public function update2date(\ActiveRecord\DateTime $dt){
        $this->last_conversation_at = $dt;
        $this->save();
        return $this;
    }
}
