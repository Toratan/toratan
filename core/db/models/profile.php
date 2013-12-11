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
     * Loads and prepare a profile
     * @param string|integer $user_id a user id
     * @return profile the fetched profile
     */
    public static function load($user_id)
    {
        $profile = parent::find($user_id);
        $profile->settings = unserialize($profile->settings);
        $profile->settings_unserialized = 1;
        return $profile;
    }
}