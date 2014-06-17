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
}
