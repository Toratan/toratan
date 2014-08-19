<?php
namespace core\db\models;

class comment extends communicationModel
{
    static $belongs_to = array(
        "note"
    );
    public function __new($comment_txt, $note_id, $user_id) {
        $comment_txt = @trim($comment_txt);
        if(!$comment_txt || !is_string($comment_txt) || !strlen($comment_txt))
            throw new \zinux\kernel\exceptions\invalidArgumentException("The comment cannot be empty");
        $this->comment = $comment_txt;
        $this->note_id = $note_id;
        $this->user_id = $user_id;
        $this->save();
        return $this;
    }
}