<?php
namespace application;
/**
* The project's bootstrapper
*/
class appBootstrap extends \zinux\kernel\application\applicationBootstrap
{
    /**
     * Validate file access rights
     * @throws \zinux\kernel\exceptions\notFoundException
     */
    public function PRE_validate_access_files(\zinux\kernel\routing\request  $request)
    {
        # if any of access file not found the .htaccess will redirect them here
        # we want to indicate not found here.
        if(preg_match("#^/access#i", $request->GetURI()))
            throw new \zinux\kernel\exceptions\notFoundException;
    }
}