<?php
namespace core\db\models;
/**
 * Note Entity
 */
class note extends item
{
    /**
     * Saves current note into database
     * @param boolean $validate Should validate?
     */
    public function save($validate=true)
    {
        # perform a normalization on note's title and body
        list($this->note_title, $this->note_body) = self::__normalize($this->note_title, $this->note_body);
        parent::save($validate);
    }
    /**
     * Fetches all sub-items under a parent directory with an owner
     * @param string $owner_id items' owner id
     * @param string $parent_id items' parent id, pass '<b>NULL </b>' to select in all parrent pattern
     * @param boolean $is_draft should it be marked as draft or not, pass '<b>item::WHATEVER</b>' to don't matter
     * @param boolean $is_public should it be public or not, pass '<b>item::WHATEVER</b>' to don't matter
     * @param boolean $is_trash should it be trashed or not, pass '<b>item::WHATEVER</b>' to don't matter
     * @param boolean $is_archive should it be archive or not, pass '<b>item::WHATEVER</b>' to don't matter
     * @param array $options see \ActiveRecord\Model::find()
     * @return array of items
     */
    public function fetchItems(
            $owner_id,
            $parent_id=NULL,
            $is_draft = self::WHATEVER,
            $is_public = self::WHATEVER, 
            $is_trash=self::WHATEVER,
            $is_archive = self::WHATEVER, 
            $options=array())
    {
        if(!isset($options["conditions"][0]))
            $options["conditions"][]  = "1";
        switch($is_draft) {
            case self::FLAG_SET:
                $options["conditions"][0] .= " AND is_draft = 1";
                break;
            case self::FLAG_UNSET:
                $options["conditions"][0] .= " AND is_draft = 0";
                break;
        }
        return parent::fetchItems($owner_id, $parent_id, $is_public, $is_trash,
                $is_archive, $options);
    }
    /**
     * Creates a new item in { title | body } datastructure
     * @param string $title the item's title
     * @param string $body the item's body
     * @param string $parent_id the item's parent id
     * @param string $owner_id the item's owner
     * @param int $editor_type The editor type ID which the note has been edited with
     * @throws \zinux\kernel\exceptions\invalidArgumentException if title not string or be empty
     * @throws \zinux\kernel\exceptions\invalidOperationException if duplication problem raise during saving item to db
     * @throws \core\db\models\Exception if any other exception raised that didn't match with previous excepions
     * @return item the create item
    */
    public function newItem(
            $title,
            $body,
            $parent_id,
            $owner_id,
            $editor_type, 
            $is_draft = self::FLAG_UNSET)
    {
        if(!isset($editor_type) || !is_numeric($editor_type) || $editor_type < 0)
            throw new \zinux\kernel\exceptions\invalidArgumentException("The `editor type` should be unsigned numeric!");
        $note = parent::newItem($title, $body, $parent_id, $owner_id);
        $note->editor_type = $editor_type;
        $note->is_draft = $is_draft;
        $note->save();
        return $note;
    }
    /**
     * Edits an item
     * @param string $item_id the item's id
     * @param string $owner_id the item's owner id
     * @param string $title string the item's title
     * @param string $body the item's body
     * @param boolean $is_public should it be public or not, pass '<b>item::NOCHANGE</b>' to don't chnage
     * @param boolean $is_trash should it be trashed or not, pass '<b>item::NOCHANGE</b>' to don't chnage
     * @param boolean $is_archive should it be archived or not, pass '<b>item::NOCHANGE</b>' to don't chnage
     * @param int $editor_type The editor type ID which the note has been edited with, if it passes the `editor_type` attribute of note will be changed, otherwise it will remaine the same.
     * @return item the edited item
     * @throws \core\db\exceptions\dbNotFoundException if the item not found
     */
    public function edit(
            $item_id,
            $owner_id,
            $title,
            $body,
            $is_draft = self::NOCHANGE,
            $is_public = self::NOCHANGE,
            $is_trash = self::NOCHANGE,
            $is_archive = self::NOCHANGE,
            $editor_type = self::NOCHANGE)
    {
        if(!is_numeric($editor_type) || $editor_type < 0)
            throw new \zinux\kernel\exceptions\invalidArgumentException("The `editor type` should be unsigned numeric!");
        $note = parent::edit($item_id, $owner_id, $title, $body, $is_public, $is_trash, $is_archive);
        if($editor_type != self::NOCHANGE)
            $note->editor_type = $editor_type;
        if($is_draft != self::NOCHANGE)
            $note->is_draft = $is_draft;
        $note->save();
        return $note;
    }
    /**
     * Normalizes the note's title and body
     * @param string $title
     * @param string $body
     * @return array A normalized array of `array($title, $body)`
     */
    public static function __normalize($title, $body, $ckeditor_normalization = 0) {
        $title = ucfirst(strip_tags(trim($title)));
        $body = ucfirst(trim($body));
        if($ckeditor_normalization)
            $body = preg_replace(
                                array(
                                        "#(?:<br\b[^>]*>|\R){1,}#i",
                                        "#<p\b[^>]*>(&nbsp;)?</p>#i"
                                ), array(
                                        "<br />\n",
                                        "",
                                ), $body);
        return array($title, $body);
    }
}