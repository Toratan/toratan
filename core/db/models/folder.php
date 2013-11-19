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
        # delete the item, because we are going re-generate the item's ID
        $deleted_item = parent::delete($item_id, $owner_id, 0);
        # creates a new item 
        $item = $this->newItem($folder_name, $deleted_item->parent_id, $owner_id);
        # modify the publicity of the item if necessary        
        if($is_public==-1)
            $item->is_public = $deleted_item->is_public;
        else
            $item->is_public = $is_public;
        # modify the trash flag of the item if necessary
        if($is_trash==-1)
            $item->is_trash = $deleted_item->is_trash;
        else
            $item->is_trash = $is_trash;
        # save the item
        $item->save();
        # return the edited item
        return $item;
    }
}