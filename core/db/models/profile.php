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

    public function after_save_unserialize_settings()
    {
        # suppress the miss-serializing errors
        $this->settings = @unserialize($this->settings);
        $this->settings_unserialized = 1;
    }

    public function before_save_serialize_settings()
    {
        $this->settings = serialize($this->settings);
        $this->settings_unserialized = 0;
    }
    /**
     * Get a profile instance with an user id
     * @param string|integer $user_id a user id
     * @return profile the fetched profile
     */
    public static function getInstance($user_id)
    {
        # fetch the profile
        $profile = parent::find($user_id);
        # un-pack the settings
        $profile->after_save_unserialize_settings();
        # validate settings instance
        if(!($profile->settings instanceof \stdClass))
            $profile->settings = new \stdClass();
        # return the profile
        return $profile;
    }
    /**
     * Set a setting
     * @param string $address the setting's address with "/" separator
     * @param mixed $value
     * @param boolean $auto_save should auto save the settings or not
     * @throws \zinux\kernel\exceptions\invalideArgumentException if $address is not string or is empty
     */
    public function setSetting($address, $value, $auto_save = 1)
    {
        # trim the address
        $address = @trim($address);
        # validate the $address input
        if(!\is_string($address) || !\strlen($address))
            throw new \zinux\kernel\exceptions\invalideArgumentException("setting's \$address is not valid....");
        # if currently no setting has been saved
        if(!($this->settings instanceof \stdClass))
            # create an instance for settings
            $this->settings = new \stdClass();
        # explode the address
        $address_partials = array_filter(\explode("/", $address));
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
        # if should auto save
        if($auto_save)
            # save the changes 
            $this->save();
    }
    /**
     * Get a setting
     * @param string $address the setting's address with "/" separator
     * @return mixed the setting's value or NULL if no setting found
     * @throws \zinux\kernel\exceptions\invalideArgumentException if $address is not string or is empty
     */
    public function getSetting($address)
    {  
        # trim the address
        $address = @trim($address);
        # validate the $address input
        if(!\is_string($address) || !\strlen($address))
            throw new \zinux\kernel\exceptions\invalideArgumentException("setting's \$address is not valid....");
        # explode the address
        $address_partials = array_filter(\explode("/", $address));
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
     * @throws \zinux\kernel\exceptions\invalideArgumentException if $address is not string or is empty
     */
    public function unsetSetting($address, $auto_save = 1)
    {  
        # trim the address
        $address = @trim($address);
        # validate the $address input
        if(!\is_string($address) || !\strlen($address))
            throw new \zinux\kernel\exceptions\invalideArgumentException("setting's \$address is not valid....");
        # explode the address
        $address_partials = array_filter(\explode("/", $address));
        # recursively delete the address
        $result = $this->recursive_deletion($address_partials, $this->settings);
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