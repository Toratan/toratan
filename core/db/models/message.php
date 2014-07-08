<?php
namespace core\db\models;

/**
 * Message Entity
 */
class message extends baseModel
{
    static $validates_presence_of = array(
        array("conversation_id", "sender_id", "receiver_id", "message_data")
    );
    /**
     * sends a message
     * @param string $sender_id the sender user id
     * @param string $reciever_id the reciever user id
     * @param string $message the content
     * @return $this
     */
    public function send($sender_id, $reciever_id, $message)
    {
        $c = conversation::open($sender_id, $reciever_id);
        $this->conversation_id = $c->conversation_id;
        $this->sender_id = $sender_id;
        $this->receiver_id = $reciever_id;
        $this->message_data = $message;
        $this->save();
        $c->update2date($this->created_at);
        return $this;
    }
    /**
     * Find last message between two users
     * @param $user_id1 The user ID
     * @param $user_id2 The user ID
     * @return message The last message
     */
    public static function last($user_id1, $user_id2)
    {
        $c = conversation::fetch($user_id1, $user_id2);
        if(!$c)
            throw new \zinux\kernel\exceptions\notFoundException("No conversation between `$user_id1` and `$user_id2` found.");
        return parent::last(array('conditions' => array('conversation_id = ?', $c->conversation_id)));
    }
    /**
     * Fetch messages based on a conversation
     * @param \core\db\models\conversation $c The target conversation instace
     * @param string $sort The sort of result(default: desc)
     * @param integer $offset (optional) The offset# for pagination
     * @param integer $limit (optional) The limit# for pagination
     * @return array Of messages
     */
    public static function fetch_by_conversation(conversation $c, $sort = "desc", $offset = -1, $limit = -1) {
    }
}
