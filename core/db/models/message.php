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
     * @return boolean
     */
    public function send($sender_id, $reciever_id, $message)
    {
        $this->sender_id = $sender_id;
        $this->reciever_id = $reciever_id;
        $this->message = $message;
        $this->save();
        return TRUE;
    }
}
