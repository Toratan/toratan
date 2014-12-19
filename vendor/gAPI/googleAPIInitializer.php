<?php
namespace vendor\gAPI;


class googleAPIInitializer extends \zinux\kernel\application\baseInitializer
{
    public function Execute() 
    {
        # invoking Google API's autoloader
        require_once 'google-api-php-client/autoload.php';
    }
}
