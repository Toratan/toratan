#!/usr/bin/php
<?php
# un-comment this when you are testing
# defined('RUNNING_ENV') || define('RUNNING_ENV', 'TEST');
require_once "start.php";
$sq = new \core\db\models\sharing_queue;
while(true)
{
    if($sq->is_empty())
    {
        sleep(1);
        continue;
    }
    expand_sharing_status($sq->read_queue());
}

function expand_sharing_status(\core\db\models\folder $folder)
{
    echo PHP_EOL, \str_repeat("-", 50), PHP_EOL;
    
    $init_sharing_status = $folder->is_public;
    
    $stack = array($folder);
    
    while(count($stack))
    {
        $folder = \array_pop($stack);
        $folder->disableAutoNotification();
        $folder->is_public = $init_sharing_status;
        echo $folder->folder_id, PHP_EOL;
        $folder->save();
        $sub_folders = $folder->fetchItems($folder->owner_id, $folder->folder_id);
        if(count($sub_folders))
            $stack = \array_merge($stack, $sub_folders);
    }
}

require_once "end.php";