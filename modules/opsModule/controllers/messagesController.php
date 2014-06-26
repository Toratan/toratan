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
        if(!is_array($c))
            throw new \zinux\kernel\exceptions\invalidOperationException("Expecting conversation list to be array!!");
        $last_messages = array();
        foreach($c as $index => $value) {
            $lm = new \stdClass;
            $m = \core\db\models\message::last($value->user1, $value->user2);
            $lm->message_data = $m->message_data;
            $lm->created_at = $m->created_at;
            $last_messages[$index] = $lm;
        }
        \zinux\kernel\utilities\debug::_var($last_messages);
        \zinux\kernel\utilities\debug::_var(\core\db\models\message::last($c[0]->user1, $c[0]->user2));
        \zinux\kernel\utilities\debug::_var(\core\db\models\profile::getBasicInformation($c[0]->user1, 0));
        \zinux\kernel\utilities\debug::_var(\core\db\models\profile::getBasicInformation($c[0]->user2, 0));
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
