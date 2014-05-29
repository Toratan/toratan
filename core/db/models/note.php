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
            $editor_type)
    {
        if(!isset($editor_type) || !is_numeric($editor_type) || $editor_type < 0)
            throw new \zinux\kernel\exceptions\invalidArgumentException("The `editor type` should be unsigned numeric!");
        $note = parent::newItem($title, $body, $parent_id, $owner_id);
        $note->editor_type = $editor_type;
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
        $note->save();
        return $note;
    }
    /**
     * Normalizes the note's title and body
     * @param string $title
     * @param string $body
     * @return array A normalized array of `array($title, $body)`
     */
    public static function __normalize($title, $body) {
        $title = ucfirst(strip_tags(trim($title)));
        $body = ucfirst(trim($body));
        return array($title, $body);
    }
}