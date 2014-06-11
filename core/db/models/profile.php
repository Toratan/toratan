<?php
namespace core\db\models;

/**
 * Profile Entity
 */
class profile extends baseModel
{
    /**
     * check if settings has been unserialized or not
     * @var boolean
     */
    protected $settings_unserialized = 0;
    /**
     * The profile's primary key column
     * @var string
     */
    static $primary_key = "user_id";
    /**
     * @var array Before save callbacks
     */
    static $before_save = array('before_save_serialize_settings');
    /**
     * @var array After save callbacks
     */
    static $after_save = array('after_save_unserialize_settings');
    
    protected static function save_in_cache($profile)
    {
        $sig = "{$profile->user_id}:".(isset($profile->settings)?"0":"1");
        $sc = new \zinux\kernel\caching\sessionCache(__CLASS__);
        /**
         * if uncommenting fetch_from_cache() uncomment this too
         */
        $sc->save($sig, /*array(*/$profile/*, time())*/);
    }
    
    protected static function fetch_from_cache($user_id, $skip_settings = 0)
    {
        $sig = "$user_id:$skip_settings";
        $sc = new \zinux\kernel\caching\sessionCache(__CLASS__);
        if($sc->isCached($sig))
        {
            $fetched = /*\array_shift*/($sc->fetch($sig/*, true*/));
            $profile = /*\array_shift*/($fetched);
            /*
             * Following couple lines makes sure the our session cache is updated with db changes but not sure
             * if is optimal.
             * 
            $res = parent::find_by_pk($user_id, array("select"=>"updated_at"));
            if($res->updated_at->getTimestamp() < \array_shift($fetched))
            */
            return $profile;
        }
        return false;
    }

    public function after_save_unserialize_settings()
    {
        # suppress the miss-serializing errors
        $this->settings = @unserialize($this->settings);
        $this->settings_unserialized = 1;
    }

    public function before_save_serialize_settings()
    {
        if($this->is_dirty())
            self::save_in_cache($this);
        $this->settings = serialize($this->settings);
        $this->settings_unserialized = 0;
    }
    /**
     * Get a profile instance with an user id
     * @param string|integer $user_id a user id(if passed NULL, the current user's user ID with be used)
     * @return profile the fetched profile
     */
    public static function getInstance($user_id = NULL, $skip_settings = 0, $use_cache = 1)
    {
        if(!$user_id) {
            if(!user::IsSignedin())
                throw new \zinux\kernel\exceptions\accessDeniedException;
            $user_id = user::GetInstance()->user_id;
        }
        if($use_cache)
        {
            $cached = self::fetch_from_cache($user_id, $skip_settings);
            if($cached !== false)
                return $cached;
        }
        # fetch the profile
        $profile = parent::find($user_id);
        # skip setting stuff?
        if($profile && !$skip_settings)
        {
            # un-pack the settings
            $profile->after_save_unserialize_settings();
            # validate settings instance
            if(!($profile->settings instanceof \stdClass))
                $profile->settings = new \stdClass();
        }
        elseif($profile)
            unset($profile->settings);
        # if using cache
        if($use_cache)
            # save the result
            self::save_in_cache($profile);
        # return the profile
        return $profile;
    }
    /**
     * Set a setting
     * @param string $address the setting's address with "/" separator
     * @param mixed $value
     * @param boolean $auto_save should auto save the settings or not
     * @throws \zinux\kernel\exceptions\invalidArgumentException if $address is not string or is empty
     */
    public function setSetting($address, $value, $auto_save = 1, $splitter = "/", $save_in_cache = 1)
    {
        # trim the address
        $address = @trim($address);
        # validate the $address input
        if(!\is_string($address) || !\strlen($address))
            throw new \zinux\kernel\exceptions\invalidArgumentException("setting's \$address is not valid....");
        # if currently no setting has been saved
        if(!($this->settings instanceof \stdClass))
            # create an instance for settings
            $this->settings = new \stdClass();
        # explode the address
        $address_partials = array_filter(\explode($splitter, $address));
        # processed address index
        $index = 0;
        # initiate a linked list instance
        $array = $this->settings;
        # iterate on address partials
        foreach($address_partials as $route)
        {
            # normalize the route
            \zinux\kernel\utilities\string::remove_special_chars($route);
            # if we have reached to dead-end on address
            if(count($address_partials) == ++$index)
                # we now have 
                $array->{$route} = $value;
            # if we gotta create a new instance
            elseif(!@$array->{$route})
                # create a new stance
                $array->{$route} = new \stdClass();
            # move to next link
            $array = $array->{$route};
        }
        # if must save in cache
        if($save_in_cache)
        {
            # then do it
            self::save_in_cache($this);
        }
        # if should auto save
        if($auto_save)
            # save the changes 
            $this->save();
    }
    /**
     * Get a setting
     * @param string $address the setting's address with "/" separator
     * @return mixed the setting's value or NULL if no setting found
     * @throws \zinux\kernel\exceptions\invalidArgumentException if $address is not string or is empty
     */
    public function getSetting($address, $splitter = "/")
    {  
        # trim the address
        $address = @trim($address);
        # validate the $address input
        if(!\is_string($address) || !\strlen($address))
            throw new \zinux\kernel\exceptions\invalidArgumentException("setting's \$address is not valid....");
        # explode the address
        $address_partials = array_filter(\explode($splitter, $address));
        # initiate a linked list instance
        $array = $this->settings;
        # iterate on address partials
        foreach($address_partials as $route)
        {
            # normalize the route
            \zinux\kernel\utilities\string::remove_special_chars($route);
            # no setting found 
            if(!@$array->{$route})
                return NULL;
            # move to next link
            $array = $array->{$route};
        }
        # return the setting value
        return $array;
    }
    /**
     * Unset a setting
     * @param string $address the setting's address with "/" separator
     * @param boolean $auto_save should auto save the settings or not
     * @return mixed the setting's value or NULL if no setting found
     * @throws \zinux\kernel\exceptions\invalidArgumentException if $address is not string or is empty
     */
    public function unsetSetting($address, $auto_save = 1, $splitter = "/", $save_in_cache = 1)
    {  
        # trim the address
        $address = @trim($address);
        # validate the $address input
        if(!\is_string($address) || !\strlen($address))
            throw new \zinux\kernel\exceptions\invalidArgumentException("setting's \$address is not valid....");
        # explode the address
        $address_partials = array_filter(\explode($splitter, $address));
        # recursively delete the address
        $result = $this->recursive_deletion($address_partials, $this->settings);
        # if must save in cache?
        if($save_in_cache)
        {
            # then do it
            self::save_in_cache($this);
        }
        # if auto-save demaned
        if($auto_save)
            # save the profile
            $this->save();
        # return the result fetched from {recursive_deletion()}
        return $result;
    }
    /**
     * Check if a setting has been set before
     * @param string $address the setting's address with "/" separator
     * @return boolean returns TRUE if setting has been set; otherwise FALSE
     */
    public function settingHasSet($address) { return $this->getSetting($address) ? TRUE : FALSE; }
    
    /**
     * recursively deletes an address from a setting
     * @return boolean TRUE if deletion was successful; otherwise FALSE
     */
    protected function recursive_deletion(&$address_partials, &$settings)
    {
        # fetch an address partial
        $m = \array_shift($address_partials);
        # if no more address partial remained
        if(!\count($address_partials))
        {
            # fail-safe for empty patrial
            if(strlen($m))
                # time to unset the setting
                unset($settings->$m);
            # indicate that deletion was successful
            return true;
        }
        # if the params does not already exists
        if(!@$settings->$m) 
            # indicate that deletion was successful
            return true;
        # dive into other parital addresses
        return $this->recursive_deletion($address_partials, $settings->$m);
    }
}