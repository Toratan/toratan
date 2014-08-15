<?php
namespace core\db\models;

class note_tag extends baseModel
{
    static $belongs_to = array(
        array('note'),
        array('tag')
    );
    /**
     * Unifies tag of a note with given {SHOULD_EXIST} tags, it will automatically tags that are missing and create tags that are new
     * @param \core\db\models\note $note The note to bind the tags to.(NOTE: the content of note will not be change during this operation
     * @param array $tags Tags that SHOULD EXIST in note
     */
    public static function unify_tagit_array(note $note, array $tags) {
        # if no tags are passed?
        if(!count($tags))
            return;
        $note->readonly();
        /**
         * calculate the deleted/new tags
         */
        $ntags = $tags;
        $otags = $note->get_tags_value();
        $new_tags = array_diff($ntags, $otags);
        $to_delete_tags = array_diff($otags, $ntags);
        # if there are already tag exist and we have `Untagged` tag among existings delete that!!
        if(count($ntags) + count($new_tags) - count($to_delete_tags) && !array_search("Untagged", $to_delete_tags))
            if(($untagged = array_search("Untagged", $new_tags)) !== FALSE) {
                # if there are any other tags beside `Untagged` tag?
                if(count($new_tags) > 1)
                    # remove the `Untagged` tag
                    unset($new_tags[$untagged]);
            }
            elseif(array_search("Untagged", $ntags)  !== FALSE)
                $to_delete_tags[] = "Untagged";
        # if there are deleted tags?
        if(count($to_delete_tags))
            # delete the deleted tags
            \core\db\models\note_tag::untagit_array($note, $to_delete_tags);
        # if there are new tags?
        if(count($new_tags))
            # add the new tags
            \core\db\models\note_tag::tagit_array($note, $new_tags);
        $note->readonly(false);
    }
    /**
     * Tags a note with elements of array, already existed are ignored
     * @param \core\db\models\note $note The note to bind the tags to.(NOTE: the content of note will not be change during this operation)
     * @param array $tags Tags that SHOULD EXIST in note
     * @return int
     */
    public static function tagit_array(note $note, array $tags) {
        $note->readonly();
        $count = 0;
        foreach($tags as $tag) {
            try{
                $nt = new self;
                $nt->note_id = $note->note_id;
                $nt->tag_id = tag::create($tag)->tag_id;
                $nt->save();
                $count++;
            } catch(\core\db\exceptions\alreadyExistsException $aee) {
               # ignore
                unset($aee);
            }
        }
        $note->readonly(false);
        return $count;
    }
    /**
     * Tag a note with a single tag value
     * @param \core\db\models\note $note The note to bind the tags to.(NOTE: the content of note will not be change during this operation)
     * @param array $tag Tag a note with it
     */
    public static function tagit(note $note, $tag) { return self::tagit_array($note, array($tag)); }
    /**
     * Untags a note from elements of array, already untagged are ignored
     * @param \core\db\models\note $note The note to unbind the tags to.(NOTE: the content of note will not be change during this operation)
     * @param array $tags Tags that SHOULD DELETE from note
     */
    public static function untagit_array(note $note, array $tags) {
        $note->readonly();
        $tags_id = array();
        foreach($note->tags as $tag) {
            if(in_array($tag->tag_value, $tags)) 
                $tags_id[] = $tag->tag_id;
        }
        # glutize the array an fetch the string format of tag ids
        $tags_id = implode(", ", $tags_id);
        # secure(escape) the tag id and re-normalize it to inject directly into QUERY 
        $tags_id = "'".implode("', '", explode(", ", substr(self::connection()->escape($tags_id), 1, strlen($tags_id))))."'";
        $builder = new \ActiveRecord\SQLBuilder(self::connection(), self::table_name());
        $builder->delete()->where("note_id = ? AND tag_id IN ($tags_id)", $note->note_id);
        self::query($builder->to_s(), $builder->bind_values());
        $note->readonly(false);
    } 
    /**
     * Untag a note from a single tag value
     * @param \core\db\models\note $note The note to unbind the tags to.(NOTE: the content of note will not be change during this operation)
     * @param array $tag Untag a note from it
     */
    public static function untagit(note $note, $tag) { return self::untagit_array($note, array($tag)); }
}