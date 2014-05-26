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
         *      {/shared} to {/default/index/shared}
         *      {/trashes} to {/default/index/trashes}
         */
        $this->addRoute("^/(archives|shared|trashes|d)$2", "/frame/e/$1$2");
        /**
         * Routes 
         *      {/new} to {/ops/new}
         *      {/edit} to {/ops/edit} 
         *      {/view} to {/ops/view}
         *      {/delete} to {/ops/delete}
         *      {/archive} to {/ops/archive}
         *      {/share} to {/ops/share}
         *      {/profile} to {/ops/profile}
         *      {/notifications} to {/ops/notifications}
         *      {/subscribe} to {/ops/subscribe}
         *      {/unsubscribe} to {/ops/unsubscribe}
         *      {/goto} to {/ops/goto}
         *      {/change/editor} to {/ops/change/editor}
         */
        $this->addRoute("^/(new|edit|view|delete|archive|share|profile|notifications|subscribe|unsubscribe|goto|change/editor)$2", "/ops/$1$2");
        /**
         * Routes
         *      {/ops/profile/avatar/crop} to {/ops/profile/avatar_crop}
         *      {/ops/profile/avatar/view} to {/ops/profile/avatar_view}
         *      {/ops/change/editor} to {/ops/change_editor}
         * Note:
         *      This route need to be after {$this->addRoute("^/(profile)$2", "/ops/$1$2");}
         *      This route need to be after {$this->addRoute("^/(change/editor)$2", "/ops/$1$2");}
         */
        $this->addRoute("^/(ops/profile/avatar|ops/change)/(crop|view|editor)$3", "$1_$2$3");
    }
}