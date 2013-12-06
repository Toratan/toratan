<?php
namespace application;
/**
* The project's router
*/
class appRoutes extends \zinux\kernel\routing\routerBootstrap
{
    public function Fetch()
    {
        /**
         * Routes 
         *      {/view} to {/ops/view}
         */
        $this->addRoute("^/view","/ops/view");
        /**
         * Routes 
         *      {/message} to {/ops/message}
         */
        $this->addRoute("^/messages","/ops/messages");
        /**
         * Routes 
         *      {/signin} to {/auth/signin}
         *      {/signup} to {/auth/signup} 
         *      {/signout} to {/auth/signout}
         */
        $this->addRoute("^/(signin|signup|signout|recovery)$2", "/auth/$1$2");
    }
}