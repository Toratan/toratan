<?php
# make script to run for ever
ignore_user_abort(true);
set_time_limit(0);
ob_start();
require_once '../public_html/index.php';
\ob_end_clean();
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