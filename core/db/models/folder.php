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
        # the only time which exception could raise in this
        # method is when $title is empty
        # since we delete the item and re-create it
        # we don't want to wast time on restoring failed edition on title's emptiness
        if(empty($folder_name) || !strlen($folder_name))
        {
           # so we fore-playing the senario here
           throw new \zinux\kernel\exceptions\dbException("Folder title cannot be blank!");
        }
        # delete the item, because we are going re-generate the item's ID
        $deleted_item = $this->fetch($item_id, $owner_id);
        if($owner_id == $deleted_item->owner_id && $folder_name == $deleted_item->folder_title)
            $item = $deleted_item;
        else
        {
            # creates a new item
            $item = $this->newItem($folder_name, $deleted_item->parent_id, $owner_id);
            # restore the creation time
            $item->created_at = $deleted_item->created_at;
            # if the creatation was success and no exception get thrown
            # delete the old item
            $this->delete($deleted_item->folder_id, $owner_id, 0);
        }
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