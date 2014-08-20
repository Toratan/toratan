<?php
namespace core\db\models;
class comment_voter extends communicationModel
{
    static $belongs_to = array(
        "comment"
    );
    /**
     * Fetches or Creates on non-existed record
     * @param $comment_id The comment ID
     * @param $user_id The user ID
     * @param boolean $create (default: 0) should if no record found?
     * @return comment_voter The fetched or created instance 
    */
    protected static function __fetch_or_create($comment_id, $user_id, $create = 1) {
        $cv = self::first(array("conditions" => array("user_id = ? AND comment_id = ?", $user_id, $comment_id)));
        if(!$cv && $create) {
            $cv = new self;
            $cv->comment_id = $comment_id;
            $cv->user_id = $user_id;
        }
        return $cv;
    }
    /**
     * Votes up a comment
     * @param $comment_id The comment ID
     * @param $user_id The user ID
     * @return comment_voter The voted instance
     */
    public static function __vote_up($comment_id, $user_id) {
        $cv = self::__fetch_or_create($comment_id, $user_id);
        $cv->is_vote_up = 1;
        $cv->save();
        return $cv;
    }
    /**
     * Votes down a comment
     * @param $comment_id The comment ID
     * @param $user_id The user ID
     * @return comment_voter The voted instance
     */
    public static function __vote_down($comment_id, $user_id) {
        $cv = self::__fetch_or_create($comment_id, $user_id);
        $cv->is_vote_up = 0;
        $cv->save();
        return $cv;
    }
    /**
     * unvote a comment
     * @param $comment_id The comment ID
     * @param $user_id The user ID
     * @return comment_voter The unvoted instance
     */
    public function __unvote($comment_id, $user_id) {
        $cv = self::__fetch_or_create($comment_id, $user_id, 0);
        if($cv) {
            $cv->delete();
        }
        return $cv;
    }
}