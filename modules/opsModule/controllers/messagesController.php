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
        if(isset($this->request->params["ajax"]))
            $this->request->params["suppress_layout"] = 1;
        if(key_exists("suppress_layout", $this->request->params))
            $this->layout->SuppressLayout();
    }
    /**
    * The modules\opsModule\controllers\messagesController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        $this->layout->AddTitle("Inbox @ Toratan");
        # a security normalization, note that we cannot use {$this->request->GetURI()} 
        # because of re-routing may happen, we need the origin URI.
        if(preg_match("#/+$#i", $_SERVER["REQUEST_URI"])) {
            header("location: /messages");
            exit;
        }
        if(!isset($this->request->params["page"]) || $this->request->params["page"] < 1)
            $this->request->params["page"] = 1;
        $fetch_limit = 10;
        $uid = \core\db\models\user::GetInstance()->user_id;
        $c = \core\db\models\conversation::fetchAll($uid, ($this->request->params["page"] - 1) * $fetch_limit, $fetch_limit);
        $this->view->is_more =\core\db\models\conversation::countAll($uid) > $this->request->params["page"] * $fetch_limit;
        if(!is_array($c))
            throw new \zinux\kernel\exceptions\invalidOperationException("Expecting conversation list to be array!!");
        $last_messages = array();
        $users = array();
        foreach($c as $index => $value) {
            $lm = new \stdClass;
            $m = \core\db\models\message::last($value->user1, $value->user2, $uid);
            $m->readonly();
            # if any last message?
            if($m) {
                $lm->message_data = $m->message_data;
                $lm->created_at = $m->created_at;
                $lm->is_read = $value->is_conversation_seen(\core\db\models\user::GetInstance()->user_id);
            } else {
                unset($c[$index]);
                continue;
                $lm->message_data = "NO MESSAGE";
                $lm->created_at = NULL;
            }
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
        $this->layout->AddTitle("Send Message");
        # we should always have a reciever
        \zinux\kernel\security\security::IsSecure($this->request->params, array('to'));
        # fetch the reciever user
        $reciever_user =\core\db\models\user::Fetch($this->request->params["to"]);
        # if no user found
        if(!$reciever_user)
            throw new \zinux\kernel\exceptions\notFoundException("The user-name `{$this->request->params["to"]}` does not exist!");
        # validate the hash-sum
        \zinux\kernel\security\security::__validate_request($this->request->params, array($reciever_user->user_id));
        # pass reciever user-info to view
        $this->view->rcv_user = $reciever_user;
        # pass sender user-info to view, which is current user
        $this->view->sender_user = \core\db\models\user::GetInstance();
        # by default there are no need for recaptcha
        $this->view->use_recaptcha = FALSE;
        # invoke a new message instance
        $m = new \core\db\models\message;
        /**
         * VALIDATING RECAPTCHA RENDERING
         */
        # fetch sender's profile
        $profile = \core\db\models\profile::getInstance($this->view->sender_user->user_id);
        # fetch last date that any message sent by sender
        $lsd = $profile->getSetting("/message/last_send_date");
        # fetch today's datetime
        $today = new \ActiveRecord\DateTime;
        # last send date count
        $lsdc = 0;
        # if there are any last message send data exists?
        if($lsd) {
            # if last message send is more than one day ago? 
            if($today->diff($lsd)->format('%a'))
                # reset last send date count
                $profile->setSetting("/message/last_send_date_count", 0, 0);
            else {
                # fetch the count since last send date
                $lsdc = $profile->getSetting("/message/last_send_date_count");
                # between every 40 message in each day
                if($lsdc && $lsdc % 40 === 0)
                    # we need recaptcha to be in effect
                    $this->view->use_recaptcha = TRUE;
            }
        }
        /**
         * /ENDOF VALIDATING RECAPTCHA RENDERING
         */
        # if not a POST req. do not proceed
        if(!$this->request->IsPOST())
            return;
        # in POST we expect to have a message as input
        \zinux\kernel\security\security::IsSecure($this->request->params, array('msg'));
        # open up a captcha handler
        $r = new \vendor\recaptcha\recaptcha;
        # if we are supposed to use captcha confermation?
        # validate the captcha
        if($this->view->use_recaptcha && !$r->is_recaptcha_valid()) {
            # if not valid do not proceed
            $this->view->error = "Invalid CAPTCHA";
            return;
        }
        # escape html chars.
        $msg = htmlspecialchars($this->request->params["msg"]);
        # if message is empty
        if(!strlen($msg))
            throw new \zinux\kernel\exceptions\invalidArgumentException("Message cannot be empty!");
        # send the message
        $m->send($this->view->sender_user->user_id, $this->view->rcv_user->user_id, $msg);
        # set last send date to TODAY
        $profile->setSetting("/message/last_send_date", $today);
        # increment last send date count
        $profile->setSetting("/message/last_send_date_count", $lsdc + 1);
        # if this is not a ajax call
        if(!isset($this->request->params["ajax"]))
            # redirect the use browser
            header("location: /@{$this->view->rcv_user->username}");
        exit;
    }

    /**
    * The \modules\opsModule\controllers\messagesController::fetch_conversationAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function fetch_conversationAction(){
        # we only response to POST requests
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\accessDeniedException;
        $this->layout->SuppressLayout();
        \zinux\kernel\security\security::IsSecure($this->request->params, array("c", "u"));
        \zinux\kernel\security\security::__validate_request($this->request->params, array($this->request->params["c"], $this->request->params["u"], session_id()));
        $c =\core\db\models\conversation::open(\core\db\models\user::GetInstance()->user_id, $this->request->params["u"], 0);
        if(!$c)
            throw new \zinux\kernel\exceptions\notFoundException;
        $uid = \core\db\models\user::GetInstance()->user_id;
        $c->marked_as_read($uid);
        $this->view->cid = $c->conversation_id;
        if(!isset($this->request->params["page"]) || $this->request->params["page"] < 1)
            $this->request->params["page"] = 1;
        $fetch_limit = 20;
        $this->view->messages = $c->fetch_messages($uid, ($this->request->params["page"] - 1) * $fetch_limit, $fetch_limit);
        $this->view->is_more =\core\db\models\message::countAll($c, $uid) > $this->request->params["page"] * $fetch_limit;
        $this->view->target_user = \core\db\models\user::Fetch($this->request->params["u"]);
        $this->view->current_user = \core\db\models\user::GetInstance();
    }

    /**
    * The \modules\opsModule\controllers\messagesController::deleteAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function delete_messagesAction()
    {
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\accessDeniedException("Unexpected request method `{$_SERVER["REQUEST_METHOD"]}`, only `POST` requests are accepted!");
        \zinux\kernel\security\security::IsSecure($this->request->params, array("messages"), array("messages" => array("\is_array", "\count")));
        \zinux\kernel\security\security::__validate_request($this->request->params);
        $msgs = $this->request->params["messages"];
        \core\db\models\message::deleteCollection(\core\db\models\user::GetInstance()->user_id, $msgs);
        exit;
    }

    /**
    * The \modules\opsModule\controllers\messagesController::delete_conversationAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function delete_conversationAction()
    {
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\accessDeniedException("Unexpected request method `{$_SERVER["REQUEST_METHOD"]}`, only `POST` requests are accepted!");
        \zinux\kernel\security\security::IsSecure($this->request->params, array("cid"), array("cid" => array("strlen")));
        \zinux\kernel\security\security::__validate_request($this->request->params, array($this->request->params["cid"]));
        $c =\core\db\models\conversation::find($this->request->params["cid"]);
        $c->deleteConversation(\core\db\models\user::GetInstance()->user_id);
        exit;
    }

    /**
    * The \modules\opsModule\controllers\messagesController::reportAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function reportAction()
    {
        # we only accept POST requests
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\accessDeniedException("Unexpected request method `{$_SERVER["REQUEST_METHOD"]}`, only `POST` requests are accepted!");
        # this should always be an ajax call, no need for layout 
        $this->layout->SuppressLayout();
        # validating data existance
        \zinux\kernel\security\security::IsSecure($this->request->params, array("type", "i"), array("type" => "strlen", "i" => "strlen"));
        # validating the request
        \zinux\kernel\security\security::__validate_request($this->request->params, array($this->request->params["i"]));
        # validating the instance type for reporting
        if(!in_array($this->request->params["type"], array("conv")))
                throw new \zinux\kernel\exceptions\invalidOperationException("Unexpected `type` value : `{$this->request->params["type"]}`");
        # if we are submiting 
        if(!isset($this->request->params["submit"])) return;
        # if submiting we need to re-validate the submited data
        \zinux\kernel\security\security::IsSecure($this->request->params, array("reportmsg"));
        # validating report type
        if(!in_array($this->request->params["reportmsg"], array("spam")))
            throw new \zinux\kernel\exceptions\invalidOperationException("Unexpected `reportmsg` value : `{$this->request->params["reportmsg"]}`");
        # define an instance for the ops
        $instance = NULL;
        # branch off the instance type
        switch($this->request->params["type"]) {
            # if we are flaging a converation
            case "conv":
                # fetch the conversation
                $instance = \core\db\models\conversation::find($this->request->params["i"]);
                break;
            default:
                throw new \zinux\kernel\exceptions\invalidOperationException("Unexpected `type` value : `{$this->request->params["type"]}`");
        }
        # fail-safe for validation
        if(!($instance instanceof \core\db\models\communicationModel))
            throw new \zinux\kernel\exceptions\invalidOperationException("Expecting \$instance to be instance of `\\core\\db\\models\\communicationModel` for type `{$this->request->params["type"]}` but wasn't.");
        # branch off the report type
        switch($this->request->params["reportmsg"]) {
            # ops for {SPAM} stuff
            case "spam":
                # mark as spam
                $instance->mark_as_spam();
                # delete the conversation
                $instance->deleteConversation(\core\db\models\user::GetInstance()->user_id);
                die("The item marked as spam successfully!");
                break;
            default:
                throw new \zinux\kernel\exceptions\invalidOperationException("Unexpected `reportmsg` value : `{$this->request->params["reportmsg"]}`");
        }
        exit;
    }
}
