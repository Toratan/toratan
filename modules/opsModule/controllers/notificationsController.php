<?php
namespace modules\opsModule\controllers;
    
/**
 * The modules\opsModule\controllers\notificationsController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class notificationsController extends \zinux\kernel\controller\baseController
{
    /**
    * The modules\opsModule\controllers\notificationsController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
    }

    /**
    * The \modules\opsModule\controllers\notificationsController::pullAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function pullAction()
    {
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\invalideOperationException;
    }
}