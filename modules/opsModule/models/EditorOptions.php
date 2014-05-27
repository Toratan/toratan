<?php
namespace modules\opsModule\models;
    
/**
* The modules\opsModule\models\EditorOptions
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class EditorOptions extends \zinux\kernel\model\baseModel{
    /**
     * parse an array into a editor options
     * @param array $options
     * @return \modules\opsModule\models\EditorOptions $this
     */
    public function __parse(array $options) {
        if(count($options) > count(get_object_vars($this)))
            throw new \zinux\kernel\exceptions\invalidArgumentException("Too much data");
        foreach(get_object_vars($this) as $key => $value) {
            if(isset($options[$key])) {
                $v = $options[$key];
                if(strtolower($v) == "on")
                    $v = true;
                $this->$key = $v;
            }
            else unset($this->$key);
        }
        return $this;
    }
}