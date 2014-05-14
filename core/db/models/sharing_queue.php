<?php
namespace core\db\models;
/**
 * sharing_queue Entity
 */
class sharing_queue extends \core\db\models\baseModel 
{
    static $belongs_to= array("folder");
    
    public static function add_queue(\core\db\models\folder $folder)
    {
        if(!$folder)
            throw new \zinux\kernel\exceptions\invalidArgumentException("The arg cannot be null!");
        $sq = new \core\db\models\sharing_queue;
        $sq->folder_id = $folder->folder_id;
        try
        {
            $sq->save();
        }
        catch(\core\db\exceptions\alreadyExistsException $aee) { unset($aee); }
    }
    public static function read_queue()
    {
        $sq = new \core\db\models\sharing_queue;
        $qitem = $sq->first(array("include"=>"folder"));
        if(!$qitem) return NULL;
        $folder = $qitem->folder;
        $qitem->delete();
        return $folder;
    }
    public static function is_empty()
    {
        $sq = new \core\db\models\sharing_queue;
        return !$sq->first();
    }
}