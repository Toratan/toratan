<?php
namespace core\db\activeRecord;


class ARInitializer extends \zinux\kernel\application\dbInitializer
{
    public function Execute($request) 
    {
        unset($request);
        # invoking AR's autoloader
        require_once 'lib/ActiveRecord.php';
    }
}
