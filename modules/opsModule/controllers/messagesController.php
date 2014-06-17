<?php
namespace modules\opsModule\controllers;
    
/**
 * The modules\opsModule\controllers\messagesController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class messagesController extends \zinux\kernel\controller\baseController
{
    /**
    * The modules\opsModule\controllers\messagesController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
    }

    /**
    * The \modules\opsModule\controllers\messagesController::sendAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function sendAction()
    {
        /**
         * TODO: craete facebook like message/conversation page
         */
        if(!$this->request->IsPOST())
            return;
        \zinux\kernel\security\security::IsSecure($this->request->params, array('u'));
        \zinux\kernel\security\security::ArrayHashCheck($this->request->params, array($this->request->params['u'], session_id()));
    }
}
