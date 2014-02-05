<?php        
    if(version_compare(\PHP_VERSION, "5.5.8", "<"))
    {
        echo ("<center>The <b>minimal</b> PHP version <b>required is 5.5.8</b>!<br />");
        echo ("Your PHP version is: <b>".\PHP_VERSION);
        die ("</b>.<br />Upgrade your server php.</center>");
    }
    session_start();
    # if we access by shell 
    # set HTTP_HOST to the script name
    @$_SERVER['HTTP_HOST'] || $_SERVER['HTTP_HOST'] = \array_shift($argv);
    # if there is any second argument passed by shell we consider it as REQUEST URI
    @$_SERVER['REQUEST_URI'] || $_SERVER['REQUEST_URI'] = count($argv) ? \array_shift($argv) : "/";
    
    defined('RUNNING_ENV') || define('RUNNING_ENV', 'DEVELOPMENT');
    
    defined('PUBLIC_HTML') || define('PUBLIC_HTML', dirname(__FILE__));
    
    defined("CACHE_PATH") || define("CACHE_PATH", PUBLIC_HTML."/../cache");
    
    defined('TORATAN_PATH') || define('TORATAN_PATH', PUBLIC_HTML."/../");
	
    defined("__SERVER_NAME__") || define("__SERVER_NAME__", $_SERVER['HTTP_HOST']);
    
    require_once PUBLIC_HTML.'/../zinux/baseZinux.php';
    
    # suppress E_STRICT error reporting
    error_reporting(E_ALL & ~E_STRICT);
    
    switch(RUNNING_ENV)
    {
        case "TEST":
        case "DEVELOPMENT":
            ini_set('display_errors','On');
            break;
        default:
            ini_set('display_errors','off');
            # manage any possible errors in production mode
            \set_error_handler(function($errno, $errstr, $errfile, $errline/*,  array $errcontext*/){
                    /* ensure that we have only one log of every error at each time */
                    static $online_cache = array();
                    if(!isset($online_cache[$errstr])) $online_cache[$errstr] = 1;
                    else return true;
                    $error_type_txt = "";
                    switch($errno)
                    {
                        case \E_STRICT:
                            if(!\preg_match("#Declaration of (.*) should be compatible with (.*)#i", $errstr))
                                /* IGNORE THE COMPATIBLE BULLSHITS */
                                break;
                            $error_type_txt = "Strict";
                            /* else fall into logging stuff */
                            goto __DEFAULT;
                        case \E_COMPILE_ERROR:
                            $error_type_txt = "Compile error";
                            /* else fall into logging stuff */       
                            goto __DEFAULT;                 
                        case \E_COMPILE_WARNING:
                            $error_type_txt = "Compile warning";
                            /* else fall into logging stuff */             
                            goto __DEFAULT;           
                        case \E_CORE_ERROR:
                            $error_type_txt = "Core error";
                            /* else fall into logging stuff */           
                            goto __DEFAULT;             
                        case \E_CORE_WARNING:
                            $error_type_txt = "Core warning";
                            /* else fall into logging stuff */      
                            goto __DEFAULT;                  
                        case \E_DEPRECATED:
                            $error_type_txt = "Deprecated";
                            /* else fall into logging stuff */      
                            goto __DEFAULT;                  
                        case \E_ERROR:
                            $error_type_txt = "Error";
                            /* else fall into logging stuff */     
                            goto __DEFAULT;                   
                        case \E_NOTICE:
                            $error_type_txt = "Notice";
                            /* else fall into logging stuff */    
                            goto __DEFAULT;                    
                        case \E_PARSE:
                            $error_type_txt = "Parse";
                            /* else fall into logging stuff */   
                            goto __DEFAULT;                     
                        case \E_RECOVERABLE_ERROR:
                            $error_type_txt = "Recoverable error";
                            /* else fall into logging stuff */   
                            goto __DEFAULT;                     
                        case \E_USER_DEPRECATED:
                            $error_type_txt = "User deprecated";
                            /* else fall into logging stuff */   
                            goto __DEFAULT;                     
                        case \E_USER_ERROR:
                            $error_type_txt = "User error";
                            /* else fall into logging stuff */   
                            goto __DEFAULT;                     
                        case \E_USER_NOTICE:
                            $error_type_txt = "User notice";
                            /* else fall into logging stuff */   
                            goto __DEFAULT;                     
                        case \E_USER_WARNING:
                            $error_type_txt = "User warning";
                            /* else fall into logging stuff */   
                            goto __DEFAULT;                     
                        case \E_WARNING:
                            $error_type_txt = "Warning";
                            /* else fall into logging stuff */   
                            goto __DEFAULT;                                       
                        default:
__DEFAULT:
                            defined("ERROR_LOG_CACHE") || define("ERROR_LOG_CACHE", \CACHE_PATH."/error.log");
                            # clear the stat cache of ERROR_LOG_CACHE
                            \clearstatcache(0, \ERROR_LOG_CACHE);
                            # if the log file size overflows from 2MB rename compress it 
                            if(\file_exists(\ERROR_LOG_CACHE) && @\filesize(\ERROR_LOG_CACHE)> 2097152 /* 2MB */)
                            {
                                # Open the gz file (w9 is the highest compression)
                                $fp = gzopen (\ERROR_LOG_CACHE.date("ymdhis").".gz", 'w9');
                                # Compress the file
                                gzwrite ($fp, file_get_contents(\ERROR_LOG_CACHE));
                                # Close the gz file and we are done
                                gzclose($fp);
                                # trucate the current file
                                \fclose(\fopen(\ERROR_LOG_CACHE, "w"));
                            }
                            # log the error
                            \error_log("[ ".date("M-d-Y H:i:s:m")." ] $error_type_txt : $errstr in $errfile on line $errline!".PHP_EOL, 3, \ERROR_LOG_CACHE);
                            /**
                             * ADVANCED: mail the owner that an error suppressed!!!
                             */
                            # flag that error handled
                            return true;
                    }
            });
            break;
    }
    
    $load = new \core\utiles\loadTime();
    $load->start();
    # suppress zinux autoloading system
    \zinux\suppress_zinux_autoloader_caching();
    
try
{
    # create an application with given module directory
    $app = new \zinux\kernel\application\application(PUBLIC_HTML.'/../modules');
    # process the application instance
    $app 
            # setting cache directory
            ->SetCacheDirectory(\CACHE_PATH)
            
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

echo "<hr /><center>Loaded in <b>{$exeTime->record($load)}</b> seconds.<br />Average load time is <b>{$exeTime->get_average_load_time()}</b> seconds.</center>";