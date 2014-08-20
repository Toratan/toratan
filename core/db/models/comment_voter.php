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
        # validate base on current voting status
        switch($cv->is_vote_up) {
            # if already down-voted?
            case 0:
                # unvote!!
                return self::__unvote($comment_id, $user_id);
            # if already up-voted?
            case 1:
                # return
                goto __RETURN;
            # otherwise
            default:
                $cv->is_vote_up = 1;
                $cv->save();
                $cv->comment->vote_up++;
                $cv->comment->save();
        }
__RETURN:
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
        # validate base on current voting status
        switch($cv->is_vote_up) {
            # if already down-voted?
            case 0:
                # return
                goto __RETURN;
            # if already up-voted?
            case 1:
                # unvote!!
                return self::__unvote($comment_id, $user_id);
            # otherwise
            default:
                $cv->is_vote_up = 0;
                $cv->save();
                $cv->comment->vote_down++;
                $cv->comment->save();
        }
__RETURN:
        return $cv;
    }
    /**
     * unvote a comment
     * @param $comment_id The comment ID
     * @param $user_id The user ID
     * @return comment_voter The unvoted instance
     */
    public static function __unvote($comment_id, $user_id) {
        $cv = self::__fetch_or_create($comment_id, $user_id, 0);
        if($cv) {
            $cv->delete();
            if($cv->is_vote_up)
                $cv->comment->vote_up--;
            else
                $cv->comment->vote_down--;
            $cv->comment->save();
        }
        return $cv;
    }
    /**
     * checks if a user has vote a comment or not?
     * @return mixed If user not voted retrurns NULL, otherwise if use up-voted returns 1 and if user down-voted returns 0.
     */
    public static function __voter_exists($comment_id, $user_id) {
        $cv = self::__fetch_or_create($comment_id, $user_id, 0);
        if(!$cv)
            return NULL;
        return $cv->is_vote_up;
    }  
}