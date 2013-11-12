<?php
namespace core\db\models;

class user extends \ActiveRecord\Model
{
    const USER_OBJECT = "USER_OBJECT";
    
    public function Signup($username, $email, $password)
    {
        foreach (array($username, $email, $password) as $param)
            if(!is_string($param) || !strlen($param))
                throw new \zinux\kernel\exceptions\invalideArgumentException;
            
        $this -> email = $email;
        $this -> username = $username;
        $this -> password = \zinux\kernel\security\hash::Generate($password,1);
        $this -> user_id = \zinux\kernel\security\hash::Generate($this->email);
        $this->save();
    }
    
    public function Fetch($username_or_email, $password)
    {
        return $this->find(
                array('conditions' => array("(username = ? OR email = ?) AND password = ?", 
                    $username_or_email,
                    $username_or_email,
                    \zinux\kernel\security\hash::Generate($password)))
        );
    }
    
    public function Signin(user $user)
    {
        $sc = new \zinux\kernel\caching\sessionCache(__CLASS__);
        $sc->save(self::USER_OBJECT, $user);
    }
    
    public static function IsSignedin()
    {
        $sc = new \zinux\kernel\caching\sessionCache(__CLASS__);
        return $sc->fetch(self::USER_OBJECT);
    }
    
    public static function Signout()
    {        
        $sc = new \zinux\kernel\caching\sessionCache(__CLASS__);
        $sc->deleteAll();
    }
    
    public static function GetInstance()
    {
        return self::IsSignedin();
    }
}