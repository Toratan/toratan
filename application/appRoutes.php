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
        $this->addRoute("^/(archives|shared|trashes|recent|d/)$2", "/frame/e/$1$2");
        /**
         * Routes
         *      {/(preview/)?@USERNAME(/posts|/about)} to {/profile/(preview/)?(posts|about)/USERNAME}
         * Note:
         *      This route needs to be before {$this->addRoute("^/(profile)$2", "/ops/$1$2");}
         */
        $this->addRoute("^/(preview/)?@([^/]+)/?(posts|about)?$4", "/profile/$1/$3/$2$4");
        /**
         * Routes
         *      {/send/message} to {/messages/send}
         * Note:
         *      This route needs to be before {$this->addRoute("^/(messages)$2", "/ops/$1$2");}
         */
        $this->addRoute("^/send/message$1", "/messages/send$1");
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
         *      {/messages} to {/ops/messages}
         *      {/comment} to {/ops/comment}
         */
        $this->addRoute("^/(new|edit|view|delete|archive|share|profile|notifications|follow|unfollow|goto|messages|comment|fetch)$2", "/ops/$1$2");
        /**
         * Routes
         *      {/ops/profile/avatar/crop} to {/ops/profile/avatar_crop}
         *      {/ops/profile/avatar/view} to {/ops/profile/avatar_view}
         * Note:
         *      This route needs to be after {$this->addRoute("^/(profile)$2", "/ops/$1$2");}
         */
        $this->addRoute("^/(ops/profile/avatar)/(crop|view)$3", "$1_$2$3");
        /**
         * Routes
         *      {/ops/profile/cover/random} to {/ops/profile/randomcover}
         *      {/ops/profile/cover/remove} to {/ops/profile/removecover}
         * Note:
         *      This route needs to be after {$this->addRoute("^/(profile)$2", "/ops/$1$2");}
         */
        $this->addRoute("^/(ops/profile)/(cover)/(random|remove)$4", "$1/$3$2$4");
        /**
         * Routes
         *      {/tag/@TAG(/@WHATEVER)?/list(/@WHATEVER)?} to {/tag/list/@TAG/@WHATEVER}
         */
        $this->addRoute("^/tag/(.*[^/])/list$2", "/tag/list/$1$2");
    }
}