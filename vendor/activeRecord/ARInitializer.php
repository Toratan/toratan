<?php
namespace vendor\activeRecord;


class ARInitializer extends \zinux\kernel\application\baseInitializer
{
    public function Execute() 
    {
        # invoking AR's autoloader
        require_once 'lib/ActiveRecord.php';
    }
}
