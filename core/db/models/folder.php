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
    public function newItem($folder_name, $parent_id, $owner_id)
    {
        return parent::newItem($folder_name, NULL, $parent_id, $owner_id);
    }
    /**
     * Edit a folder
     * @param string $item_id the item's ID
     * @param string $owner_id the item's owner's ID
     * @param string $folder_name the new folder's name
     * @param boolean $is_public should it mark as public folder, assign to -1 to leave it as is
     * @param boolean $is_trash should it mark as trashed folder, assign to -1 to leave it as is
     */
    public function edit($item_id, $owner_id, $folder_name, $is_public = -1, $is_trash = -1)
    {
        parent::edit($item_id, $owner_id, $folder_name, NULL, $is_public, $is_trash);
    }
}