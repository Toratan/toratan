<?php
namespace application;
/**
* The project's router
*/
class appRoutes extends \zinux\kernel\routing\routerBootstrap
{
    public function Fetch()
    {
        $this->routeMessages();
    }
    /**
     * Routes {/message} to {/ops/message}
     */
    protected function routeMessages()
    {
        $this->addRoute("^/messages","/ops/messages");
    }
}