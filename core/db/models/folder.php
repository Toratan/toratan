<?php
namespace core\db\models;

/**
 * Folder Entity
 */
class folder extends item
{
    /**
     * Creates new folder
     * @param string $folder_name the folder name
     * @param string $parent_id the folder's parent id
     * @param string $owner_id the folder's owner id
     */
    public function newItem(
            $folder_name,
            $parent_id,
            $owner_id)
    {
        return parent::newItem($folder_name, NULL, $parent_id, $owner_id);
    }
    /**
     * Edits a folder
     * @param string $item_id the item's id
     * @param string $owner_id the item's owner id
     * @param string $title string the item's title
     * @param string $body the item's body
     * @param boolean $is_public should it be public or not, pass '<b>item::NOCHANGE</b>' to don't chnage
     * @param boolean $is_trash should it be trashed or not, pass '<b>item::NOCHANGE</b>' to don't chnage
     * @param boolean $is_archive should it be archived or not, pass '<b>item::NOCHANGE</b>' to don't chnage
     * @throws \core\db\exceptions\dbNotFound if the item not found
     */
    public function edit(
            $item_id,
            $owner_id,
            $folder_name,
            $is_public = self::NOCHANGE,
            $is_trash = self::NOCHANGE,
            $is_archive = self::NOCHANGE)
    {
        parent::edit($item_id, $owner_id, $folder_name, NULL, $is_public, $is_trash, $is_archive);
    }
}
