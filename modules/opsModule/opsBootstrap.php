<?php
namespace modules\opsModule;
/**
* The opsModule's Bootstrapper
*/
class opsBootstrap
{
    /**
     * Checks session signin sig. and make exceptions for some routes to pass through without signing in.
     * @param \zinux\kernel\routing\request $request
     */
    public function pre_signin_check(\zinux\kernel\routing\request $request)
    {
        # if user already signed in?
        if(\core\db\models\user::IsSignedin())
            # no need for check
            return;
        # a list of exception array({controller => array(actions)}) which does not need signin sig.
        $signin_free_ops = array(
            "index"  => array("view", "goto", "explorer"),
            "profile" => array("index", "about", "posts", "avatar_view"),
            "fetch"   => array("comment", "popular", "related")
        );
        # the normalized currently requested {conttroller => action} 
        $current_request = array(
            # the pure controller name without the suffix
            "controller" => strtolower($request->controller->name), 
            # the pure action name without the suffix 
            "action"       => strtolower($request->action->name)
        );
        # if current request's matches with an index in the signin free list
        if(isset($signin_free_ops[$current_request["controller"]]) && in_array($current_request["action"], $signin_free_ops[$current_request["controller"]]))
            return;
        # otherwise
__MUST_SIGNIN:
        # we need to signin 
        # we will also pass the continue spot to auth module
        header("location: /auth/signin?continue={$request->GetPrimaryURI()}");
        # halt the PHP
        exit;
    }
}