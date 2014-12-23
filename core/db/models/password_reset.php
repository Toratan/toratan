<?php
namespace core\db\models;

class password_reset extends \core\db\models\baseModel
{
    public static function __create_request_id($email, $second_to_expire = 0) {
        $ins = new self;
        $ins->password_reset_id =\zinux\kernel\security\hash::Generate($email);
        $ins->user_email = $email;
        if($second_to_expire)
            $ins->expires_at = time() + $second_to_expire;
        $ins->save();
        return $ins->password_reset_id;
    }
}