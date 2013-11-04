<?php
namespace core\db\activeRecord;

# invoking AR's autoloader
require_once 'lib/ActiveRecord.php';

class ARInitializer extends \zinux\kernel\application\dbInitializer
{
    public function Execute($request) {}
}
