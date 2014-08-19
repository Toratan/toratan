<?php
namespace core\db\models;

/**
 * A abstract communication model
 * @author dariush
 */
abstract class communicationModel extends abstractModel {
    
    const MARKED_AS_NORMAL = 0x0;
    const MARKED_AS_SPAM      = 0x1;
    /**
     * Mark the current instance as spam
     * @param boolean $auto_save should the method do autosave?
     */
    public function mark_as_spam($auto_save = 1) {
        $this->marked_as = self::MARKED_AS_SPAM;
        if($auto_save)
            $this->save();
    }
}
?>
