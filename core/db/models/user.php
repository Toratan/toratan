<?php
namespace core\db\models;

class user extends baseModel
{
    static $has_many = array(
       array("folders", 'foreign_key' => 'owner_id'), 
       array("notes", 'foreign_key' => 'owner_id'), 
       array("links", 'foreign_key' => 'owner_id'), 
    );
    /**
     * Normal user type flag
     */
    const USER_TYPE_NORMAL = 0x0;
    /**
     * Administrator user type flag
     */
    const USER_TYPE_ADMINISTRATOR = 0x1;
    /**
     * Developer user type flag
     */
    const USER_TYPE_DEVELOPER = 0x2;
    static $has_one = array(
        array("profile", "select" => "user_id, first_name, nick_name, last_name")
    );
    static $validates_presence_of = array(
        array('username'),
        array('email'),
        array('password') 
    );
    /**
     * User object alias in this class' cache registery
     */
    const USER_OBJECT = "USER_OBJECT";
    /**
     * The root user's ID#
     */
    const ROOT_USER_ID = "0";
    
    /**
     * Signs up a new user
     * @param string $username the new user's username
     * @param string $email the new user's email
     * @param strign $password the new user's password
     * @throws \zinux\kernel\exceptions\invalidArgumentException is any of params were not string or an empty passed through
     * @throws \core\exceptions\exceptionCollection a collection exception that produced during the signup opt.
     */
    public function Signup($username, $email, $password)
    {
        # define a new execption collector
        $ec = new \core\exceptions\exceptionCollection;
        # validate username
        if(!strlen($username))
            $ec->addException(new \zinux\kernel\exceptions\invalidArgumentException("Username cannot be empty!"));
        elseif(preg_match('#[^a-z0-9]#i', $username))
            $ec->addException (
                new \zinux\kernel\exceptions\invalidArgumentException(
                    "Username '$username' contains <a href='http://en.wikipedia.org/wiki/Special_characters' title='See wikipedia' target='__blank'>special characters</a>!"));
        # validate email address
        # validate username
        if(!strlen($email))
            $ec->addException(new \zinux\kernel\exceptions\invalidArgumentException("Email cannot be empty!"));
        elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
            $ec->addException (new \zinux\kernel\exceptions\invalidArgumentException("Email '$email' is not a valid email address!"));
        # validate password
        if(empty($password))
            $ec->addException(new \zinux\kernel\exceptions\invalidArgumentException("Password cannot be empty!"));
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
    public static function Fetch($username_or_email_or_userID, $password = NULL)
    {
        # find the user with its username or email and password
        $cond = array("(username = ? OR email = ? OR user_id = ?)", 
                    $username_or_email_or_userID,
                    $username_or_email_or_userID,
                    $username_or_email_or_userID);
        if($password)
        {
            $cond[0].= " AND password = ?";
            $cond[] = \zinux\kernel\security\hash::Generate($password);
        }
        return parent::find("first", array('conditions' => $cond));
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
    /**
     * Returns user's realname in capitalized format if any exist; otherwise return the user's username
     * @param bool $full_name TRUE if should be full name; FALSE if only want first name
     * @param bool $restrict if the output name is empty throw exception?
     * @return string
     */
    public function get_RealName_or_Username($full_name = 1, $restrict = 1) {
        if(!@$this->profile) {
            $this->Signout();
            throw new \zinux\kernel\exceptions\securityException("The user's profile with ID `{$this->user_id}` were not accessable.");
        }
        $fn = $this->profile->first_name;
        if($full_name)
            $fn = "$fn {$this->profile->last_name}";
        $fn = ucwords(strtolower($fn));
        if(!strlen($fn))
            $fn = $this->user_name;
        if($restrict && !strlen($fn))
            throw new \zinux\kernel\exceptions\appException("Empty name not expected!");
        return $fn;
    }
}