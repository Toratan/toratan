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
        $c = \core\db\models\conversation::fetchAll(\core\db\models\user::GetInstance()->user_id);
        \zinux\kernel\utilities\debug::_var(\core\db\models\profile::getBasicInformation($c[0]->user1, 0));
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
