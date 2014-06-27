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
        $uid = \core\db\models\user::GetInstance()->user_id;
        $c = \core\db\models\conversation::fetchAll($uid);
        if(!is_array($c))
            throw new \zinux\kernel\exceptions\invalidOperationException("Expecting conversation list to be array!!");
        $last_messages = array();
        $users = array();
        foreach($c as $index => $value) {
            $lm = new \stdClass;
            $m = \core\db\models\message::last($value->user1, $value->user2);
            $lm->message_data = $m->message_data;
            $lm->created_at = $m->created_at;
            $last_messages[$index] = $lm;
            if($value->user1 != $uid)
                $users[$index] =  \core\db\models\profile::getBasicInformation($value->user1);
            else
                $users[$index] =  \core\db\models\profile::getBasicInformation($value->user2);
        }
        $this->view->conv_ids = $c;
        $this->view->conv_users = $users;
        $this->view->conv_last_message = $last_messages;
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
