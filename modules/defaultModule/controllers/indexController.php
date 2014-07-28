<?php
namespace modules\defaultModule\controllers;
    
/**
 * The modules\defaultModule\controllers\indexController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class indexController extends \zinux\kernel\controller\baseController
{
    /**
    * The modules\defaultModule\controllers\indexController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
    }

    /**
    * The \modules\defaultModule\controllers\indexController::tagAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function tagAction()
    {
        if(!$this->request->CountIndexedParam())
            throw new \zinux\kernel\exceptions\invalidArgumentException("No tag value passed");
        \zinux\kernel\utilities\debug::_var($this->request->params, 1);
    }
}
