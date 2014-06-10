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
        # a list of exception array({controller => action}) which does not need signin sig.
        $signin_free_ops = array(
            array("index", "view"),
            array("index", "goto"),
            array("index", "explorer"),
            array("profile", "index"),
            array("profile", "avatar_view"),
        );
        # the normalized currently requested {conttroller => action} 
        $current_request = array(
            # the pure controller name without the suffix
            strtolower($request->controller->name), 
            # the pure action name without the suffix 
            strtolower($request->action->name)
        );
        # if current request's matches with an index in the signin free list
        foreach ($signin_free_ops as $pair)
        {
            if(!is_array($pair)) throw new \zinux\kernel\exceptions\invalidOperationException("expecting `pair` to be array");
            $controller = $pair[0];
            $action = $pair[1];
            # proceed to checking actions
            if(
                strtolower($controller) == $current_request[0] &&
                strtolower($action) == $current_request[1]
            )
                # if it matches, we are OK
                # no need to signin
                return;
        }
        # if the PC didn't changed in previous lines
        # we need to signin 
        # we will also pass the continue spot to auth module
        header("location: /auth/signin?continue={$request->GetURI()}");
        # halt the PHP
        exit;
    }
}