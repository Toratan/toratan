<?php
namespace core\db\models;

class comment extends communicationModel
{
    static $belongs_to = array(
        "note"
    );
    static $has_many = array(
       array("comment_voter", "class_name" => "comment_voter")
    );
    /**
     * Creates new comment
     * @param string $comment_txt The comment text
     * @param integer $note_id The note ID to comment to.
     * @param string $user_id The comment's owner's user ID
     * @return \core\db\models\comment The created comment
     */
    public static function __new($comment_txt, $note_id, $user_id) {
        $comment_txt = @trim($comment_txt);
        if(!$comment_txt || !is_string($comment_txt) || !strlen($comment_txt))
            throw new \zinux\kernel\exceptions\invalidArgumentException("The comment cannot be empty");
        $c = new self;
        $c->comment = $comment_txt;
        $c->note_id = $note_id;
        $c->user_id = $user_id;
        $c->save();
        return $c;
    }
    /**
     * fetches top comments
     * @param integer $note_id The ID of note for loading comments
     * @param integer $offset (default: 0) The offset
     * @param integer $limit (default: 10) The limit for query
     * @return array of comments
     */
    public static function __fetch_top($note_id, $offset = 0, $limit = 10) {
        $builder = self::getSQLBuilder();
        $builder
                ->select("*")
                ->where("note_id = ? AND marked_as = ?", $note_id, self::MARKED_AS_NORMAL)
                # every one down-vote will erase an up votes
                # the {.01} portion in the 1 factor is for when a comment voteup and votedowns are
                # equally greater than zero the comment can be distinguished from zero voted comments
                # by this query.
                ->order("vote_up * 1.01 - vote_down DESC, created_at DESC")
                ->offset($offset)
                ->limit($limit);
        return self::find_by_sql($builder->to_s(), $builder->bind_values());
    }
    /**
     * fetches totall count of comments bound with a note ID
     * @param integer $note_id The target note ID
     * @return integer The count of comments associated with the note ID 
     */
    public static function  __fetch_count($note_id) { return self::count(array("conditions" => array("note_id = ? AND marked_as = ?", $note_id, self::MARKED_AS_NORMAL))); }
    /**
     * Deletes a comment
     * @param integer $note_id The note ID to comment to.
     * @param integer $comment_id The comment ID to delete
     * @param string $user_id The comment's owner's user ID.
     * @return integer The affected row
     */
    public static function __delete($note_id, $comment_id, $user_id) {return self::delete_all(array("conditions"=>array("note_id = ? AND user_id = ? AND comment_id = ?", $note_id, $user_id, $comment_id)));} 
    /**
     * finds a comment with accurate details
     * @param integer $note_id The note ID to comment to.
     * @param integer $comment_id The comment ID to delete
     * @param string $user_id The comment's owner's user ID.
     * @return comment The found comment
     */
    public static function __find($note_id, $comment_id) { return self::first(array("conditions"=>array("comment_id = ? AND note_id = ?", $comment_id, $note_id))); }
}