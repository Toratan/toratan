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
        /**
         * Routes 
         *      {/new} to {/ops/new}
         *      {/edit} to {/ops/edit} 
         *      {/view} to {/ops/view}
         *      {/delete} to {/ops/delete}
         *      {/archive} to {/ops/archive}
         *      {/share} to {/ops/share}
         */
        $this->addRoute("^/(new|edit|view|delete|archive|share)$2", "/ops/$1$2");
    }
}