<?php
namespace vendor\PHPMailer;


class phpMailerInitializer extends \zinux\kernel\application\baseInitializer
{
    public function Execute() 
    {
        # invoking PHPMailer's autoloader
        require_once 'lib/PHPMailerAutoload.php';
    }
}
