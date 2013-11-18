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
       parent::newItem($folder_name, NULL, $parent_id, $owner_id);
    }
}