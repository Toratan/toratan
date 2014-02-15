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
        $this->layout->SuppressLayout();
        if(false && !$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\invalideOperationException;
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
}
