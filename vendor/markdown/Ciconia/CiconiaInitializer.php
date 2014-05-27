<?php
namespace vendor\markdown\Ciconia;


class CiconiaInitializer extends \zinux\kernel\application\baseInitializer
{
    public function Execute() 
    {
        # invoking AR's autoloader
        require_once 'vendor/autoload.php';
    }
}
