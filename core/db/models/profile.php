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
        $this->settings = unserialize($this->settings);
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
        $profile = parent::find($user_id);
        # suppress the miss-serializing errors
        $profile->settings = @unserialize($profile->settings);
        $profile->settings_unserialized = 1;
        if(!($profile->settings instanceof \stdClass))
            $profile->settings = new \stdClass();
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
        # open up a session cache socket 
        $sc = new \zinux\kernel\caching\sessionCache(__CLASS__."::Settings");
        # cache the setting
        $sc->save($address, $value);
    }
    /**
     * Get a setting
     * @param string $address the setting's address with "/" separator
     * @return mixed the setting's value
     * @throws \zinux\kernel\exceptions\invalideArgumentException if $address is not string or is empty
     */
    public function getSetting($address)
    {  
        # trim the address
        $address = @trim($address);
        # validate the $address input
        if(!\is_string($address) || !\strlen($address))
            throw new \zinux\kernel\exceptions\invalideArgumentException("setting's \$address is not valid....");
        # open up a session cache socket 
        $sc = new \zinux\kernel\caching\sessionCache(__CLASS__."::Settings");
        # if already cached?
        if($sc->isCached($address))
            # return the cache
            return  $sc->fetch($address);
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
        # save the setting's value
        $sc->save($address, $array);
        # return the setting value
        return $array;
    }
}