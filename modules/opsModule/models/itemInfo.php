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
        if(!$item) throw new \zinux\kernel\exceptions\invalidArgumentException("item cannot be null");
        $s = array_merge(
            array(
                $item->{"{$item->WhoAmI()}_id"},
                $item->is_public?"0":"1",
                $item->is_archive?"0":"1",
            )
        );
        return implode(";", $s);
    } 
    /**
     * decode an item's information
     * @param string $info The information
     * @param boolean @see json_decode
     * @return mixed @see json_decode
     */
    public static function decode($info, $assoc = false) {
        if(!is_string($info))
            throw new \zinux\kernel\exceptions\invalidArgumentException("expecting `info` be a string");
        $e = explode(";", $info);
        if(count($e) !== 3) throw new \zinux\kernel\exceptions\invalidArgumentException("Invalid format");
        $s = array_combine(array("i", "s", "a"), $e);
        if($assoc) return $s;
        return ((object)$s);
    }
}