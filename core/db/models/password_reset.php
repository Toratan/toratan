<?php
namespace core\db\models;

class password_reset extends \core\db\models\baseModel
{
    /**
     * Creates a new password reset request
     * @param $email The email to be request should created upon
     * @param $second_to_expire The expiration time of request(pass 0 for no expiration)
     * @return string The generated request ID
     */
    public static function __create_request_id($email, $second_to_expire = 0) {
        $ins = new self;
        $ins->password_reset_id =\zinux\kernel\security\hash::Generate($email);
        $ins->user_email = $email;
        if($second_to_expire)
            $ins->expires_at = date(\ActiveRecord\DateTime::get_format(), time() + $second_to_expire);
        $ins->save();
        return $ins->password_reset_id;
    }
    /**
     * Deletes a password reset request
     * @param $request_id The password reset request ID
     * @return The affected rows
     */
    public static function __delete_request($request_id) {
        # validate the input
        \zinux\kernel\security\security::IsSecure(array("r" => $request_id), array(), array("r" => array("is_string", "strlen")));
        # delete the request (note that the request ID is unique)
        return self::delete_all(array("conditions" => array("password_reset_id = ?", $request_id)));
    }
    /**
     * fetches a user instance related to a request ID
     * @param $request_id The password reset request ID
     * @param boolean $auto_delete should delete automatically after fetching the user
     * @return user
     * @throws \zinux\kernel\exceptions\notFoundException if the request is not really found or expired
     */
    public static function __get_user_from_request_id($request_id, $auto_delete = 1) {
        # validate the input
        \zinux\kernel\security\security::IsSecure(array("r" => $request_id), array(), array("r" => array("is_string", "strlen")));
        # try to find the request
        $ins = self::find(array("conditions" => array("password_reset_id = ?", $request_id)));
        # if no record exists
        if(!$ins)
            throw new \zinux\kernel\exceptions\notFoundException;
        # if the record not found?
        if(strtotime($ins->expires_at) != 0 && strtotime($ins->expires_at) < strtotime(date(\ActiveRecord\DateTime::get_format()))) {
            # delete the instance
            $ins->delete();
            # throw a not found record
            throw new \zinux\kernel\exceptions\notFoundException;
        }
        # if auto-delete
        if($auto_delete)
            # delete the instance
            $ins->delete();
        # in here we are good to go
        return user::Fetch($ins->user_email);
    }
}