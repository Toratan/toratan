<?php
namespace application;
/**
* The project's bootstrapper
*/
class appBootstrap extends \zinux\kernel\application\applicationBootstrap
{
    /** 
     * setups db using activerecord 
     */
    public function PRE_db_setup()
    {
        # init activerecord configs
        \ActiveRecord\Config::initialize(function($cfg)
        {
            # fetching db related configurations
            $dbcfg = \zinux\kernel\application\config::GetConfig("toratan", "db");
            # setting connection string
            $cfg->set_connections(array(
                RUNNING_ENV =>  
                    "{$dbcfg["type"]}://{$dbcfg["username"]}:{$dbcfg["password"]}@{$dbcfg["host"]}/{$dbcfg["name"]}?charset=utf8")
            );
           # enable the connection string as to RUNNING_ENV
            $cfg->set_default_connection(RUNNING_ENV);
        });
        # testing db connection
        \ActiveRecord\Connection::instance();
        # if we reach here we are all OK
    }
}