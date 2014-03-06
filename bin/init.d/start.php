<?php
# make script to run for ever
ignore_user_abort(true);
set_time_limit(0);
# include zinux framework
require_once "../zinux/baseZinux.php";
# validate the arguments
if($argc < 3) {
    \file_put_contents("{$argv[0]}.error", "No log or error file specified!\nAborting");
    exit;
}
/**
 * log erros to error file
 * @param string $error
 */
function log_error($error)
{
    global $argv;
    \file_put_contents($argv[2], $error.PHP_EOL, \FILE_APPEND);
}
/**
 * log output to log file
 * @param string $error
 */
function log_output($log)
{
    global $argv;
    \file_put_contents($argv[1], $log.PHP_EOL, \FILE_APPEND);
}