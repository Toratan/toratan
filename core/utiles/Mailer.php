<?php
namespace core\utiles;

/**
 * Description of Mailer
 *
 * @author dariush
 */
class Mailer extends \PHPMailer
{
    public function __construct($user_name, $password, $exceptions=true)
    {
        \zinux\kernel\security\security::IsSecure(
                array('u'=>$user_name, 'p' =>$password), 
                array(),
                array('u' => array('is_string', 'strlen'), 'p' => 'is_string'));
        parent::__construct($exceptions);
        $this->isSMTP();
        $this->Host = \zinux\kernel\application\config::GetConfig("toratan.mail.host");
        $this->SMTPAuth = true;
        $this->Port     = \zinux\kernel\application\config::GetConfig("toratan.mail.port");
        $this->SMTPSecure = \zinux\kernel\application\config::GetConfig("toratan.mail.protocol");
        $this->Username = $user_name;
        $this->Password  = $password;
        # add the sender address
        $this->setFrom("$user_name@".\zinux\kernel\application\config::GetConfig("toratan.domain"), 'Toratan');
    }
}
