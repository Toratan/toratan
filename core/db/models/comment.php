<?php
namespace core\db\models;

class comment extends communicationModel
{
    static $belongs_to = array(
        "note"
    );
    /**
     * Creates new comment
     * @param string $comment_txt The comment text
     * @param integer $note_id The note ID to comment to.
     * @param string $user_id The comment's owner's user ID
     * @return \core\db\models\comment $this
     */
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
                ->where("note_id = ?", $note_id)
                # every two download will raise the affect of up votes
                # the {.01} portion in the 2 factor is for when a comment voteup and votedowns are
                # equally greater than zero the comment can be distinguished from zero voted comments
                # by this query.
                ->order("vote_up * 2.01 - vote_down")
                ->offset($offset)
                ->limit($limit);
        return self::find_by_sql($builder->to_s(), $builder->bind_values());
    }
    /**
     * fetches totall count of comments bound with a note ID
     * @param integer $note_id The target note ID
     * @return integer The count of comments associated with the note ID 
     */
    public static function  __fetch_count($note_id) { return self::count(array("conditions" => array("note_id = ?", $note_id))); }
}