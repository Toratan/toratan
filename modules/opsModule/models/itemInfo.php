<?php
namespace modules\opsModule\models;
    
/**
* The modules\opsModule\models\itemInfo
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class itemInfo extends \zinux\kernel\model\baseModel
{
    public static function encode(\core\db\models\item $item) {
        if(!$item) throw new \zinux\kernel\exceptions\invalideArgumentException("item cannot be null");
        $s = array_merge(
            array(
                $item->WhoAmI() => $item->{"{$item->WhoAmI()}_id"},
                "share" => $item->is_public?"0":"1",
                "archive" => !$item->is_archive?"0":"1",
                "trash" => $item->is_trash?"0":"1",
            ), 
            \zinux\kernel\security\security::GetHashArray(array($item->WhoAmI(),  $item->{"{$item->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id))
        );
        return json_encode($s);
    } 
    public static function decode($info) {
        if(!is_string($info))
            throw new \zinux\kernel\exceptions\invalideArgumentException("expecting `info` be a string");
        return json_decode($info, true);
    }
}