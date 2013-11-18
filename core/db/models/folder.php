<?php
namespace core\db\models;

class folder extends item
{
    public function newItem($folder_name, $parent_id, $user_id)
    {
       parent::newItem($folder_name, NULL, $parent_id, $user_id);
    }
}