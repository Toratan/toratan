<?php        
    session_start();
    # if we access by shell 
    # set HTTP_HOST to the script name
    @$_SERVER['HTTP_HOST'] || $_SERVER['HTTP_HOST'] = \array_shift($argv);
    # if there is any second argument passed by shell we consider it as REQUEST URI
    @$_SERVER['REQUEST_URI'] || $_SERVER['REQUEST_URI'] = count($argv) ? \array_shift($argv) : "/";
    
    defined('RUNNING_ENV') || define('RUNNING_ENV', 'DEVELOPMENT');
    
    defined('PUBLIC_HTML') || define('PUBLIC_HTML', dirname(__FILE__));
    
    defined('TORATAN_PATH') || define('TORATAN_PATH', PUBLIC_HTML."/../");
	
    defined("__SERVER_NAME__") || define("__SERVER_NAME__", $_SERVER['HTTP_HOST']);
    
    switch(RUNNING_ENV)
    {
        case "TEST":
        case "DEVELOPMENT":
            ini_set('display_errors','On');
            error_reporting(E_ALL);
            break;
        default:
            ini_set('display_errors','off');
            error_reporting(E_ERROR);
            break;
    }

    require_once PUBLIC_HTML.'/../zinux/baseZinux.php';
    $load = new \core\utiles\loadTime();
    $load->start();
    # make memCache to handle autoload caching
    \zinux\set_zinux_autoloader_caching_handler(1);
    # suppress zinux autoloading system
    \zinux\suppress_zinux_autoloader_caching();
    \zinux\set_zinux_autoloader_memCache_options(array("save_on_destruction" => 1));
    
try
{
    # create an application with given module directory
    $app = new \zinux\kernel\application\application(PUBLIC_HTML.'/../modules');
    # process the application instance
    $app 
            # setting cache directory
            ->SetCacheDirectory(PUBLIC_HTML."/../cache")
            
            # setting router's bootstrap which will route /note/:id:/edit => /note/edit/:id:
            ->SetRouterBootstrap(new \application\appRoutes)
            
            # set application's bootstrap 
            ->SetBootstrap(new application\dbBootstrap)
            
            # init activerecord as db handler
            ->SetDBInitializer(new \core\db\activeRecord\ARInitializer())
            
            # load project basic config initializer
            ->SetConfigIniliazer(new \zinux\kernel\utilities\iniParser(PROJECT_ROOT."/config/default.cfg", RUNNING_ENV))
            # register php markdown parser 
            # repo : https://github.com/michelf/php-markdown
            ->registerPlugin("PHP-MARKDOWN", "/core/ui/markdown/lib")
            # register socket-raw
            # repo : https://github.com/clue/socket-raw
            ->registerPlugin("SOCKET-RAW", "/core/vendors/socket-raw")
            # init the application's optz.
            ->Startup()
            # run the application 
            ->Run()
            # shutdown the application
            ->Shutdown();
}
# catch any thing from application
catch(Exception $e)
{
    /**
     * You can redirect this exception to a controller e.g /error
     */
    echo "<legend>Oops!</legend>";
    echo "<p>Error happened ...</p>";
    echo "<p><b>Message: </b></p><p>";
    require_once PROJECT_ROOT.'zinux/kernel/utilities/debug.php';
    zinux\kernel\utilities\debug::_var($e->getMessage());
    echo "</p>";
    echo "<p><b>Stack Trace: </b></p><pre>".$e->getTraceAsString()."</pre>";
    zinux\kernel\utilities\debug::_var($e->getTrace());
}
$exeTime = new \core\db\models\execution;

echo "<center>Loaded in <b>{$exeTime->record($load)}</b> seconds.<br />Average load time is <b>{$exeTime->get_average_load_time()}</b> seconds.</center>";