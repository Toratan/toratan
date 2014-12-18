<?php
namespace modules\opsModule\controllers;
    
/**
 * The modules\opsModule\controllers\notificationsController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class notificationsController extends \zinux\kernel\controller\baseController
{
    /**
     * Redirects header to pointed URL
     * @param string $this->request->params["continue"] if $this->request->params["continue"]
     * provided it will set the header location to the point, otherwise redirects to site's root
     */
    protected function Redirect()
    {
        $params = $this->request->params;
        if(headers_sent())
            return false;
        if(isset($params["continue"]))
        {
            header("location: {$params["continue"]}");
            exit;
        }
        return false;
    }
    /**
    * The modules\opsModule\controllers\notificationsController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction() { throw new \zinux\kernel\exceptions\accessDeniedException; }

    /**
    * The \modules\opsModule\controllers\notificationsController::pullAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function pullAction()
    {
        throw new \zinux\kernel\exceptions\notImplementedException;
        $this->layout->SuppressLayout();
        if(false && !$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\invalidOperationException;
        if(!@$this->request->params["since"])
            $this->request->params["since"] = 0;
        # $limit variable for fecthing notifs
        if(!@$this->request->params["l"])
            $this->request->params["l"] = 10;
        # $offset variable for fecthing notifs
        if(!@$this->request->params["o"])
            $this->request->params["o"] = 0;
        # $type of notifs
        if(!@$this->request->params["t"])
            $this->request->params["t"] = -1;
        # fetch notifs
        $this->view->notifs = 
            \core\db\models\notification::fetch_json(
                \core\db\models\user::GetInstance()->user_id,
                $this->request->params["l"],
                $this->request->params["o"],
                1,
                $this->request->params["t"],
                $this->request->params["since"]);
        # update last pull time to NOW()
        \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id)->setSetting("/notifications/pull/last_time", date("M-d-Y H:i:s"));
        if(!@$this->request->params["html"])
            die($this->view->notifs);
        echo date("M-d-Y H:i:s:m", $this->request->params["since"]);
        \zinux\kernel\utilities\debug::_var($this->request->params["since"]);
        \zinux\kernel\utilities\debug::_var(\json_decode($this->view->notifs));
        \trigger_error("Security concerns, for providing solid general hashing style for pulling notifs!!");
    }

    /**
    * The \modules\opsModule\controllers\notificationsController::clearAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function clearAction()
    {
        \zinux\kernel\security\security::IsSecure($this->request->params, array("o", "l"), array("o" => "is_numeric", "l" => "is_numeric"));
        \zinux\kernel\security\security::__validate_request($this->request->params, array($this->request->params["o"], $this->request->params["l"], \core\db\models\user::GetInstance()->user_id));
        $n = new \core\db\models\notification;
        $n->clear_all(\core\db\models\user::GetInstance()->user_id, $this->request->params["l"], $this->request->params["o"]);
        die;
    }
    /**
     * creates an instance based on incomming request
     * @return \core\db\models\baseModel
     * @throws \zinux\kernel\exceptions\invalidArgumentException if the requested type is not supported
     */
    protected function __get_item_instance() {
        if(!in_array($this->request->GetIndexedParam(0), array("note")))
                throw new \zinux\kernel\exceptions\invalidArgumentException("Invliad request.");
        $type =$this->request->GetIndexedParam(0);
        \zinux\kernel\security\security::__validate_request($this->request->params, array($type, $this->request->params[$type], session_id()));
        $item_class = "\\core\\db\\models\\{$type}";
        $ins = new $item_class;
        return $ins->fetch($this->request->params[$type], \core\db\models\user::GetInstance()->user_id);
    }
    /**
    * The \modules\opsModule\controllers\notificationsController::stopAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function stopAction() { 
        # stop the notification
        $ins = $this->__get_item_instance(); $ins->get_notification = 0; $ins->save();
        # make ouput
        $pipe = new \core\utiles\messagePipe;
        $pipe->write("You will not get any more notification from this.");
        # redirect and exit
        $this->Redirect(); exit;
    }

    /**
    * The \modules\opsModule\controllers\notificationsController::startAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function startAction() { 
        # stop the notification
        $ins = $this->__get_item_instance(); $ins->get_notification = 1; $ins->save();
        # make ouput
        $pipe = new \core\utiles\messagePipe;
        $pipe->write("Notifications has been activated for this.");
        # redirect and exit
        $this->Redirect(); exit;
    }
}
