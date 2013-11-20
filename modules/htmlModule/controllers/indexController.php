<?php
namespace modules\htmlModule\controllers;

/**
 * The modules\htmlModule\controllers\indexController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class indexController extends \zinux\kernel\controller\baseController
{
    /**
    * The modules\htmlModule\controllers\indexController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        if(!\core\db\models\user::IsSignedin()) return;
        $this->layout->AddTitle("Home");
        $f = new \core\db\models\folder();
        if(!isset($this->request->params["directory"]))
            $this->request->params["directory"] = 0;
        $this->view->pid = $pid = $this->request->params["directory"];
        $uid = \core\db\models\user::GetInstance()->user_id;
        $this->view->folders = ($f->fetchItems($pid, $uid, -1, 0));
        $n = new \core\db\models\note;
        $this->view->notes = ($n->fetchItems($pid, $uid, -1, 0));
        $l = new \core\db\models\link;
        $this->view->links = ($l->fetchItems($pid, $uid, -1, 0));
    }

    /**
    * The \modules\htmlModule\controllers\indexController::trashesAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function trashesAction()
    {
        if(!\core\db\models\user::IsSignedin()) return;
        $this->layout->AddTitle("Trashes");
        $f = new \core\db\models\folder();
        if(!isset($this->request->params["directory"]))
            $this->request->params["directory"] = 0;
        $this->view->pid = $pid = $this->request->params["directory"];
        $uid = \core\db\models\user::GetInstance()->user_id;
        $this->view->folders = ($f->fetchTrashes($uid));
        $n = new \core\db\models\note;
        $this->view->notes = ($n->fetchTrashes($uid));
        $l = new \core\db\models\link;
        $this->view->links = ($l->fetchTrashes($uid));
    }

    /**
    * The \modules\htmlModule\controllers\indexController::archivesAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function archivesAction()
    {
        
    }
}
