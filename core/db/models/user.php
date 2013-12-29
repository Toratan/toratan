<?php
namespace core\db\models;

class user extends baseModel
{
    static $has_one = array(
        array("profile", "select" => "first_name, nick_name, last_name")
    );
    static $validates_presence_of = array(
        array('username'),
        array('email'),
        array('password') 
    );
    static $validates_numericality_of = array(
        array('is_deactive', 'less_than_or_equal_to' => 1, 'greater_than_or_equal_to' => 0)
    );
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
     * @throws \core\exceptions\exceptionCollection a collection exception that produced during the signup opt.
     */
    public function Signup($username, $email, $password)
    {
        # define a new execption collector
        $ec = new \core\exceptions\exceptionCollection;
        # validate username
        if(!strlen($username))
            $ec->addException(new \zinux\kernel\exceptions\invalideArgumentException("Username cannot be empty!"));
        elseif(preg_match('#[^a-z0-9]#i', $username))
            $ec->addException (
                new \zinux\kernel\exceptions\invalideArgumentException(
                    "Username '$username' contains <a href='http://en.wikipedia.org/wiki/Special_characters' title='See wikipedia' target='__blank'>special characters</a>!"));
        # validate email address
        # validate username
        if(!strlen($email))
            $ec->addException(new \zinux\kernel\exceptions\invalideArgumentException("Email cannot be empty!"));
        elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
            $ec->addException (new \zinux\kernel\exceptions\invalideArgumentException("Email '$email' is not a valid email address!"));
        # validate password
        if(empty($password))
            $ec->addException(new \zinux\kernel\exceptions\invalideArgumentException("Password cannot be empty!"));
        # throw if any exception collected
        $ec->ThrowCollected();
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
     * @param string $password the user's email or password if password no set it only will search by username or email
     * @return user
     */
    public function Fetch($username_or_email, $password = NULL)
    {
        # find the user with its username or email and password
        $cond = array("(username = ? OR email = ?)", 
                    $username_or_email,
                    $username_or_email);
        if($password)
        {
            $cond[0].= " AND password = ?";
            $cond[] = \zinux\kernel\security\hash::Generate($password);
        }
        return $this->find("first", array('conditions' => $cond));
    }
    /**
     * Signin's users into its session
     * @param \core\db\models\user $user the target user to register
     * @param boolean $set_cookie set to the cookie!?
     * @param integer $expire_from_now expire time from now, the deafult is for a full year from now
     */
    public function Signin(user $user, $set_cookie = 0, $expire_from_now = 31536000)
    {
        # open up a session cache related to this class
        $sc = new \zinux\kernel\caching\sessionCache(__CLASS__);
        # store the USER_OBJECT into session cache
        $sc->save(self::USER_OBJECT, $user);
        # if we don't want to set cookie, just return
        if(!$set_cookie)
            return;
        # cryption isntance
        $crpt = new \zinux\kernel\security\cryption();
        # cryption key
        $crpt_key = \zinux\kernel\security\hash::Generate(\core\db\models\user::USER_OBJECT);
        # cookie name
        $cookie_name = \zinux\kernel\security\hash::Generate(\core\db\models\user::USER_OBJECT);
        # encrypt the user's ID
        self::setCookie($cookie_name, $crpt->encrypt($user->user_id, $crpt_key), $expire_from_now);
        
    }
    /**
     * Check if the users is signed in or not
     * @return user NULL if user has not signed in, otherwise the user's instace
     */
    public static function IsSignedin()
    {
        if(self::GetInstance())
            return TRUE;
        # cryption isntance
        $crpt = new \zinux\kernel\security\cryption();
        # secure cookie instance
        $sec_cookie = new \zinux\kernel\security\secureCookie; 
        # cryption key
        $crpt_key = \zinux\kernel\security\hash::Generate(\core\db\models\user::USER_OBJECT);
        # cookie name
        $cookie_name = \zinux\kernel\security\hash::Generate(\core\db\models\user::USER_OBJECT);
        # if cookie contains the user's ID
        if($sec_cookie->contains($cookie_name))
        {
            # decrypt the user's ID 
            $user_id = $crpt->decrypt($_COOKIE[$cookie_name], $crpt_key);
            # fetch the user
            $fetched_user = self::find("first", array("conditions"=>array("user_id = ?", $user_id)));
            # if not found?
            if(!$fetched_user)
            {
                # delete the user's ID from cookie, it's invalid either
                $sec_cookie->delete($cookie_name,  "/", __SERVER_NAME__);
                # not FOUND
                return  FALSE;
            }
            else
            {
                # if we find the user
                # sign in the user
                self::Signin($fetched_user);
            }
        }
        return self::GetInstance() != NULL;
    }
    /**
     * Signout the user from its session
     */
    public static function Signout()
    {        
        # destroy all session data
        \session_destroy();
        # destroy current session array
        unset($_SESSION);
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
    /**
    * Set a domain-wide cookie 
    * @link http://php.net/manual/en/function.setcookie.php
    * @param string $name cookie's name
    * @param string $value cookie's value
    * @param integer $expire_from_now expire time from now, the deafult is for a full year from now
    * @param string $path cookie's path default is /
    * @param string $host cookie's domain default would be $_SERVER["HTTP_HOST"]
    */
    public static function setCookie($name, $value = null,  $expire_from_now = 31536000, $path = "/", $domain = null, $secure = false, $httponly = false)
    {
        if(!$domain)
            $domain = $_SERVER["HTTP_HOST"];
        # secure cookie instance
        $sec_cookie = new \zinux\kernel\security\secureCookie; 
        # set the cookie
        $sec_cookie->set($name, $value, $expire_from_now, $path, $domain, $secure, $httponly);
    }
}