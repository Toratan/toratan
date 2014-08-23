<?php
namespace core\db\vendors;
/**
* The modules\opsModule\models\itemStatus
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class itemStatus extends \zinux\kernel\model\baseModel {
    /**
     * @var boolean
     */
    public $is_public = 0;
    /**
     * @var boolean
     */
    public $is_archive = 0;
    /**
     * @var boolean
     */
    public $is_trash = 0;
    /**
     * encodes item's status
     * @param \core\db\models\item $item
     * @return real The integer flag according to item's status
     */
    public static function encode(\core\db\models\item $item){
        # lock the item
        $item->readonly();
        # numberic pattern for this format will be { 1, 9, 5, 13, 3, 11, 7, 15 }
        # the default value
        $flag = 0b0001;
        # is_public ever selected?
        if(isset($item->is_public) && $item->is_public)
            $flag = $flag | 0b1000;
        # is_archive ever selected?
        if(isset($item->is_archive) && $item->is_archive)
            $flag = $flag | 0b0100;
        # is_trash ever selected?
        if(isset($item->is_trash) && $item->is_trash)
            $flag = $flag | 0b0010;
        # unlock the item
        $item->readonly(false);
        # return the flag
        return $flag;
    }
    /**
     * decodes the encoded item
     * @param real $status The status encoded by itemStatus
     * @return itemStatus The item status
     */
    public static function decode($status) {
        # fail safe for current version
        if(!($status & 0b0001) || $status > 15)
            throw new \zinux\kernel\exceptions\invalidArgumentException("Invalid status format `".decbin($status)."`");
        $s = new self;
        $s->is_public   = ($status & 0b1000) ? 1 : 0;
        $s->is_archive = ($status & 0b0100) ? 1 : 0;
        $s->is_trash     = ($status & 0b0010) ? 1 : 0;
        return $s;
    }
    /**
     * compares the difference between an itemStatus status and current instance's status
     * @param self $status
     * @return itemStatus An itemStatus, if the any propery of return instance is TRUE it mean property has
     * different values in both itemStatus
     */
    public function diff(itemStatus $status) {
        $s = new self;
        $s->is_public   = ($this->is_public   != $status->is_public) ? 1 : 0;
        $s->is_archive = ($this->is_archive != $status->is_archive) ? 1 : 0;
        $s->is_trash     = ($this->is_trash    != $status->is_trash) ? 1 : 0;
        return $s;
    }
}