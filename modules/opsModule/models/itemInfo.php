<?php
namespace modules\opsModule\models;
    
/**
* The modules\opsModule\models\itemInfo
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class itemInfo extends \zinux\kernel\model\baseModel
{
    /**
     * encode an item's attrib
     * @param \core\db\models\item $item Target item
     * @return string The json encoded string
     */
    public static function encode(\core\db\models\item $item) {
        if(!$item) throw new \zinux\kernel\exceptions\invalideArgumentException("item cannot be null");
        $s = array_merge(
            array(
                $item->WhoAmI() => $item->{"{$item->WhoAmI()}_id"},
                "share" => $item->is_public?"0":"1",
                "archive" => $item->is_archive?"0":"1",
                "trash" => $item->is_trash?"0":"1",
            ), 
            \zinux\kernel\security\security::GetHashArray(array($item->WhoAmI(),  $item->{"{$item->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id))
        );
        return json_encode($s);
    } 
    /**
     * decode an item's information
     * @param string $info The information
     * @param boolean @see json_decode
     * @return mixed @see json_decode
     */
    public static function decode($info, $assoc = false) {
        if(!is_string($info))
            throw new \zinux\kernel\exceptions\invalideArgumentException("expecting `info` be a string");
        return json_decode($info, $assoc);
    }
}