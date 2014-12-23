<?php
namespace core\db\models;

class password_reset extends \core\db\models\baseModel
{
    public static function __create_request_id($email) {
        $ins = new self;
        $ins->password_reset_id =\zinux\kernel\security\hash::Generate($email);
        $ins->user_email = $email;
        $ins->save();
        return $ins->password_reset_id;
    }
}