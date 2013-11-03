<?php
namespace modules\defaultModule;
/**
* The defaultModule's Bootstrapper
*/
class defaultBootstrap
{
    /**
     * A pre-dispatch function
     * @param \zinux\kernel\routing\request $request
     */
    public function pre_CHECK(\zinux\kernel\routing\request $request)
    {
        header("location: /html".$_SERVER['REQUEST_URI']);
        exit;
    }
    /**
     * A post-dispatch function
     * @param \zinux\kernel\routing\request $request
     */
    public function post_CHECK(\zinux\kernel\routing\request $request)
    {
    }
}