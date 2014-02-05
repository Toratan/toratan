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
         *      {/signin} to {/auth/signin}
         *      {/signup} to {/auth/signup} 
         *      {/signout} to {/auth/signout}
         */
        $this->addRoute("^/(signin|signup|signout|recovery)$2", "/auth/$1$2");
        /**
         * Inorder to neutralize the next router on route {/default/index/acrhives} aka {/archives} we 
         * need to modify to original uri by explicitly addressing the {/:modules:/:controller:} in uri.
         * Roures
         *      {/archives} to {/default/index/archives}
         */
        $this->addRoute("^/(archives|shared)$2", "/default/index/$1$2");
        /**
         * Routes 
         *      {/new} to {/ops/new}
         *      {/edit} to {/ops/edit} 
         *      {/view} to {/ops/view}
         *      {/delete} to {/ops/delete}
         *      {/archive} to {/ops/archive}
         *      {/share} to {/ops/share}
         *      {/messages} to {/ops/messages}
         *      {/profile} to {/ops/profile}
         *      {/notifications} to {/ops/notifications}
         *      {/subscribe} to {/ops/subscribe}
         *      {/unsubscribe} to {/ops/unsubscribe}
         */
        $this->addRoute("^/(new|edit|view|delete|archive|share|messages|profile|notifications|subscribe|unsubscribe)$2", "/ops/$1$2");
        /**
         * Routes
         *      {/ops/profile/avatar/crop} to {/ops/profile/avatar_crop}
         * Note:
         *      This route need to be after {$this->addRoute("^/(profile)$2", "/ops/$1$2");}
         */
        $this->addRoute("^(/ops/profile/avatar)/(crop|view)$3", "$1_$2$3");
    }
}