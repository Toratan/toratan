<?php
namespace application;
/**
* The project's bootstrapper
*/
class appBootstrap extends \zinux\kernel\application\applicationBootstrap
{
    public function PRE_validate_access_files(\zinux\kernel\routing\request  $request)
    {
        # if any of access file not found the .htaccess will redirect them here
        # we want to indicate not found here.
        if(\strtolower($request->GetIndexedParam(0)) === "access")
        {
            header('HTTP/1.1 404 FILE NOT FOUND');
            exit;
        }
    }
}