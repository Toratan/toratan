<?php
namespace core\utiles;
if(!\defined("TORATAN_PATH"))
    \trigger_error("Class `\\".__NAMESPACE__."\\".basename(__FILE__, ".php")."` only can access by origin toratan project!", E_USER_ERROR);

defined("SCRIPT_RUNNER_PATH") || define("SCRIPT_RUNNER_PATH", TORATAN_PATH."/scripts/run");
defined("SCRIPT_HANDLER_PATH") || define("SCRIPT_HANDLER_PATH", TORATAN_PATH."/scripts/public_html/index.php");

/**
 * Runs an script 
 */
class script
{
    /**
     * Runs a script 
     * @param string $script_uri the script URI we want to run
     * @param boolean $run_at_background should the script run at background
     * @return boolean TRUE on background scripts if script launched successfully, or the script output on non-background scripts
     * @throws \zinux\kernel\exceptions\notFoundException if script handlers not found
     */
    public static function run($script_uri, $run_at_background = 1)
    {
        # if we are doing a background script 
        # validate script runner existence
        if($run_at_background && !\file_exists(SCRIPT_RUNNER_PATH))
            throw new \zinux\kernel\exceptions\notFoundException("Script runner not found!");
        # if we are NOT doing a background script 
        # validate script handler existence
        if(!$run_at_background && !\file_exists(SCRIPT_HANDLER_PATH))
            throw new \zinux\kernel\exceptions\notFoundException("Scripts handler not found!");
        # run the script
        if($run_at_background)
            \exec(SCRIPT_RUNNER_PATH." $script_uri");
        else
            \exec("php ".SCRIPT_HANDLER_PATH." $script_uri", $output);
        # indicate the success
        return $run_at_background ? TRUE : \implode("\n", $output);
    }
}
