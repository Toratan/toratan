<?php
namespace core\db\models;

class user extends \ActiveRecord\Model
{
    /**
     * User object alias in this class' cache registery
     */
    const USER_OBJECT = "USER_OBJECT";
    
    /**
     * Signs up a new user
     * @param string $username the new user's username
     * @param string $email the new user's email
     * @param strign $password the new user's password
     * @throws \zinux\kernel\exceptions\invalideArgumentException is any of params were not string or an empty passed through
     */
    public function Signup($username, $email, $password)
    {
        # checkup function's params
        foreach (array($username, $email, $password) as $param)
            if(!is_string($param) || !strlen($param))
                throw new \zinux\kernel\exceptions\invalideArgumentException;
        # add email
        $this -> email = $email;
        # add username
        $this -> username = $username;
        # add hashed password
        $this -> password = \zinux\kernel\security\hash::Generate($password,1);
        # generate a new user ID
        $this -> user_id = \zinux\kernel\security\hash::Generate($this->email . $this->username);
        # save into database
        $this->save();
    }
    /**
     * Fetches a user's info by either its username or its email and its password 
     * @param string $username_or_email The users email or username 
     * @param string $password the user's email or password
     * @return user
     */
    public function Fetch($username_or_email, $password)
    {
        # find the user with its username or email and password
        return $this->find(
                array('conditions' => array("(username = ? OR email = ?) AND password = ?", 
                    $username_or_email,
                    $username_or_email,
                    \zinux\kernel\security\hash::Generate($password)))
        );
    }
    /**
     * Signin's users into its session
     * @param \core\db\models\user $user the target user to register
     */
    public function Signin(user $user)
    {
        # open up a session cache related to this class
        $sc = new \zinux\kernel\caching\sessionCache(__CLASS__);
        # store the USER_OBJECT into session cache
        $sc->save(self::USER_OBJECT, $user);
    }
    /**
     * Check if the users is signed in or not
     * @return user NULL if user has not signed in, otherwise the user's instace
     */
    public static function IsSignedin()
    {
        # returns the value of user's instance
        return self::GetInstance();
    }
    /**
     * Signout the user from its session
     */
    public static function Signout()
    {        
        # open up a session cache related to this class
        $sc = new \zinux\kernel\caching\sessionCache(__CLASS__);
        # delete entire cached data
        $sc->deleteAll();
    }
    /**
     * Fetches users info from its session
     * @return user NULL if user has not signed in, otherwise the user's instace
     */
    public static function GetInstance()
    {
        # open up a session cache related to this class
        $sc = new \zinux\kernel\caching\sessionCache(__CLASS__);
        # return the USER_OBJECT stored in the session
        return $sc->fetch(self::USER_OBJECT);
    }
}