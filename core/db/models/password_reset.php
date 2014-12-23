<?php
namespace core\db\models;

class password_reset extends \core\db\models\baseModel
{
    public static function __create_request_id($email, $second_to_expire = 0) {
        $ins = new self;
        $ins->password_reset_id =\zinux\kernel\security\hash::Generate($email);
        $ins->user_email = $email;
        if($second_to_expire)
            $ins->expires_at = date(\ActiveRecord\DateTime::get_format(), time() + $second_to_expire);
        $ins->save();
        return $ins->password_reset_id;
    }
    public static function __get_user_from_request_id($request_id, $auto_delete = 1) {
        \zinux\kernel\security\security::IsSecure(array("r" => $request_id), array(), array("r" => array("is_string", "strlen")));
        $ins = self::find(array("conditions" => array("password_reset_id = ?", $request_id)));
        # if no record exists
        if(!$ins)
            throw new \zinux\kernel\exceptions\notFoundException;
        # if the record not found?
        if(strtotime($ins->expires_at) < strtotime(date(\ActiveRecord\DateTime::get_format())))
            throw new \zinux\kernel\exceptions\notFoundException;
        # if auto-delete
        if($auto_delete)
            # delete the instance
            $ins->delete();
        # in here we are good to go
        return user::Fetch($ins->user_email);
    }
}