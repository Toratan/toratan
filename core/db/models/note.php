<?php
namespace core\db\models;

/**
 * Note Entity
 */
class note extends item
{
    public function newItem($title, $body, $parent_id, $owner_id, $editor_type)
    {
        if(!isset($editor_type) || !is_numeric($editor_type))
            throw new \zinux\kernel\exceptions\invalidArgumentException("The `editor type` should be numeric!");
        $note = parent::newItem($title, $body, $parent_id, $owner_id);
        $note->editor_type = $editor_type;
        $note->save();
        return $note;
    }
    public function edit($item_id, $owner_id, $title, $body,
            $is_public=self::NOCHANGE, $is_trash=self::NOCHANGE,
            $is_archive=self::NOCHANGE, $editor_type = "undefined")
    {
        if(strtolower($editor_type) !== "undefined" && !is_numeric($editor_type))
            throw new \zinux\kernel\exceptions\invalidArgumentException("The `editor type` should be numeric!");
        $note = parent::edit($item_id, $owner_id, $title, $body, $is_public, $is_trash, $is_archive);
        if(strtolower($editor_type) !== "undefined")
            $note->editor_type = $editor_type;
        $note->save();
        return $note;
    }
}