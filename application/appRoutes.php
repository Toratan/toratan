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
        $this->routeOpsView();
    }
    /**
     * Routes {/message} to {/ops/message}
     */
    protected function routeMessages()
    {
        $this->addRoute("^/messages","/ops/messages");
    }
    /**
     * Routes {/view} to {/ops/view}
     */
    protected function routeOpsView()
    {
        $this->addRoute("^/view","/ops/view");
    }
}