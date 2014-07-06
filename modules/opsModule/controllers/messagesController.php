<?php
namespace modules\opsModule\controllers;
    
/**
 * The modules\opsModule\controllers\messagesController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class messagesController extends \zinux\kernel\controller\baseController
{
    public function Initiate()
    {
        parent::Initiate();
        if(key_exists("suppress_layout", $this->request->params))
            $this->layout->SuppressLayout();
    }
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
        # we should always have a reciever
        \zinux\kernel\security\security::IsSecure($this->request->params, array('to'));
        # fetch the reciever user
        $reciever_user =\core\db\models\user::Fetch($this->request->params["to"]);
        # if no user found
        if(!$reciever_user)
            throw new \zinux\kernel\exceptions\notFoundException("The user-name `{$this->request->params["to"]}` does not exist!");
        # validate the hash-sum
        \zinux\kernel\security\security::ArrayHashCheck($this->request->params, array($reciever_user->user_id));
        # pass reciever user-info to view
        $this->view->rcv_user = $reciever_user;
        # pass sender user-info to view, which is current user
        $this->view->sender_user = \core\db\models\user::GetInstance();
        # if not a POST req. do not proceed
        if(!$this->request->IsPOST())
            return;
        # in POST we expect to have a message as input
        \zinux\kernel\security\security::IsSecure($this->request->params, array('msg'));
        # escape html chars.
        $msg = htmlspecialchars($this->request->params["msg"]);
        # if message is empty
        if(!strlen($msg))
            throw new \zinux\kernel\exceptions\invalidArgumentException("Message cannot be empty!");
        # invoke a new message instance
        $m = new \core\db\models\message;
        # send the message
        $m->send($this->view->sender_user->user_id, $this->view->rcv_user->user_id, $msg);
        # if this is not a ajax call
        if(!isset($this->request->params["ajax"]))
            # redirect the use browser
            header("location: /@{$this->view->rcv_user->username}");
        exit;
    }
}
