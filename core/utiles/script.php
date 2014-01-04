<?php
namespace core\utiles;

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
        if($run_at_background && !\file_exists(PROJECT_ROOT."scripts/run"))
            throw new \zinux\kernel\exceptions\notFoundException("Script runner not found!");
        
        # if we are NOT doing a background script 
        # validate script handler existence
        if(!$run_at_background && !\file_exists(PROJECT_ROOT."scripts/public_html/index.php"))
            throw new \zinux\kernel\exceptions\notFoundException("Scripts handler not found!");
        # run the script
        if($run_at_background)
            \shell_exec(PROJECT_ROOT."scripts/run $script_uri");
        else
            return \shell_exec("php ".PROJECT_ROOT."scripts/public_html/index.php $script_uri");
        # indicate the success
        return TRUE;
    }
}
