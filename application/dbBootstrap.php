<?php
namespace application;
/**
* The project's bootstrapper
*/
class dbBootstrap extends \zinux\kernel\application\applicationBootstrap
{
    const TORATAN = RUNNING_ENV;
    const TORATAN_SCRIPT = "TORATAN_SCRIPTS";
    /** 
     * setups db using activerecord 
     */
    public static function PRE_toratan_db_setup()
    {
        # init activerecord configs
        \ActiveRecord\Config::initialize(function($cfg)
        {
            # fetching db related configurations
            $dbcfg = \zinux\kernel\application\config::GetConfig("toratan", "db");
            # fetching db related to scripts configurations
            $dbcfg_scripts = \zinux\kernel\application\config::GetConfig("toratan", "scripts", "db");
            # setting connection string
            $cfg->set_connections(array(
                    \application\dbBootstrap::TORATAN =>  
                            "{$dbcfg["type"]}://{$dbcfg["username"]}:{$dbcfg["password"]}@{$dbcfg["host"]}/{$dbcfg["name"]}?charset=utf8",
                    \application\dbBootstrap::TORATAN_SCRIPT => 
                            "{$dbcfg_scripts["type"]}://{$dbcfg_scripts["username"]}:{$dbcfg_scripts["password"]}@{$dbcfg_scripts["host"]}/{$dbcfg_scripts["name"]}?charset=utf8")
            );
           # enable the connection string as to RUNNING_ENV
            $cfg->set_default_connection(\application\dbBootstrap::TORATAN);
        });
        # testing db connection
        \ActiveRecord\Connection::instance();
        # if we reach here we are all OK
    }
    /** 
     * switches database mode between {\application\dbBootstrap::TORATAN} <=> {\application\dbBootstrap::TORATAN_SCRIPTS}
     */
    public static function switch_database_mode($mode)
    {
        switch($mode)
        {
            case self::TORATAN:
            case self::TORATAN_SCRIPT:
                \ActiveRecord\Config::instance()->set_default_connection($mode);
                break;
            default:
                throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid mode supplied!");
        }
    }
}