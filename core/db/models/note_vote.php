<?php
namespace core\db\models;

class note_vote extends baseModel
{
    static $belongs_to = array(
        array('note'),
        array('user')
    );
    /**
     * Votes a note
     * @param $note_id The note ID
     * @param $user_id The voter's user ID
     * @param numerical $vote The given vote
     */
    public function vote($note_id, $user_id, $vote) {
        $nv = $this;
        $vote = floor($vote);
        if($vote <= 0)
            throw new \zinux\kernel\exceptions\invalidOperationException("The vote cannot be less or equal to zero; '$vote' is given!");
        $nv->note_id = $note_id;
        $nv->user_id = $user_id;
        $nv->vote = $vote;
        $n_nv = $nv->note->vote_value;
        $n_nc = $nv->note->vote_count;
        try{
            $nv->save();
            $nv->note->vote_value = ($n_nc * $n_nv + $vote) / ($n_nc + 1);
            $nv->note->vote_count++;
        } catch(\core\db\exceptions\alreadyExistsException $aee) {
            unset($aee);
            $nv = self::find("first", array("conditions" => array("note_id = ? AND user_id = ?", $note_id, $user_id)));
            $nv->note->vote_value = ($n_nc * $n_nv - $nv->vote + $vote) / ($n_nc);
            $nv->vote = $vote;
            $nv->save();
        }
        $nv->note->save();
        return $nv;
    }
    /**
     * Un-votes a note
     * @param $note_id The note ID
     * @param $user_id The un-voter's user ID
     * @retrun true if successful
     */
    public function  unvote($note_id, $user_id) {
        $nv = self::find("first", array("conditions" => array("note_id = ? AND user_id = ?", $note_id, $user_id)));
        if(!$nv) return true;
        $n_nv = $nv->note->vote_value;
        $n_nc = $nv->note->vote_count;
        $nv->note->vote_value = ($n_nc == 1 ? 0 : ($n_nc * $n_nv - $nv->vote) / ($n_nc - 1));
        $nv->note->vote_count = ($n_nc <= 1 ? 0 : $n_nc - 1);
        $nv->note->save();
        return $nv->delete();
    }
    /**
     * Checks if a note has been voted by a user or not
     * @param $note_id The note ID
     * @param $user_id The voter's user ID
     * @return If the user is voted then the voted value will be returned, Otherwise it will return 0
     */
    public function is_voted($note_id, $user_id) {
        $v = self::find("first", array("conditions" => array("note_id = ? AND user_id = ?", $note_id, $user_id), "select" => "vote"));
        return is_null($v) ? 0 : $v->vote;
    }
}