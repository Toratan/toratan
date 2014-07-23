<?php
namespace core\db\models;

/**
 * Message Entity
 */
class message extends communicationModel
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
     * @param $current_user The current user id which is reviewing the last message?(SHOULD BE ONE OF $user_id1 OR $user_id2)
     * @return message The last message
     */
    public static function last($user_id1, $user_id2, $current_user)
    {
        if($user_id1 !== $current_user && $user_id2 !== $current_user)
            throw new \zinux\kernel\exceptions\invalidArgumentException("\$current_user SHOULD BE ONE OF \$user_id1 OR \$user_id2");
        $c = conversation::fetch($user_id1, $user_id2);
        if(!$c)
            throw new \zinux\kernel\exceptions\notFoundException("No conversation between `$user_id1` and `$user_id2` found.");
        return parent::last(array('conditions' => array('conversation_id = ? AND (deleted_id IS NULL OR deleted_id NOT IN (?))', $c->conversation_id, $current_user)));
    }
    /**
     * Delete a collection of messsages at point of view a user id, if both the sender and the reciever of the message
     * happen to delete the same message the message will actually get deleted from database, but if only one of them
     * delete the message the message will be invisible to that user when using `conversation::fetch_messages()`
     * method to fetch messages
     * @param string $user_id The user id that messages are belong to.(i.e as sender or as reciever of the message)
     * @param array $messages_id the array messages' IDs.
     */
    public static function deleteCollection($user_id, array $messages_id) {
        # glutize the array an fetch the string format of message ids
        $messages_id = implode(", ", $messages_id);
        # secure(escape) the message id and re-normalize it to inject directly into QUERY 
        $messages_id = "'".implode("', '", explode(", ", substr(self::connection()->escape($messages_id), 1, strlen($messages_id))))."'";
        # delete messages with given ID collection which both users are deleted them 
        self::delete_all(array('conditions' => array("(sender_id = ? OR receiver_id = ?) AND message_id in (?) AND deleted_id IS NOT NULL AND deleted_id != ?", $user_id, $user_id, $messages_id, $user_id)));
        # QUERY a update
        # NOTE: don't enject $message_id in format of *?* in the QUERY, PAR will get a pair of *'* around it and everything will be mess. $message_id is already secured in some line above.
        self::query(
                "UPDATE  `".(\ActiveRecord\Utils::pluralize(str_replace(__NAMESPACE__."\\", "", __CLASS__)))."` SET deleted_id =  ? WHERE (sender_id = ? OR receiver_id = ?) AND message_id IN ($messages_id) AND deleted_id IS NULL",
                array($user_id, $user_id, $user_id));
    }
    /**
     * Counts a conversation's messages
     * @param $user_id The user's ID for whom is fetching for
     * @return integer # of conversations's messages
     */
    public static function countAll(conversation $conversation, $user_id, $non_deleted = 1) {
        $conversation->readonly();
        $cond = array('conversation_id = ?', $conversation->conversation_id);
        if($non_deleted) {
            $cond[0] .= " AND (deleted_id IS NULL OR deleted_id != ?)";
            $cond[] = $user_id;
        }
        $conversation->readonly(false);
        return parent::count(array('conditions' => $cond));
    }
}
