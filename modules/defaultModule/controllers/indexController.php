<?php
namespace modules\defaultModule\controllers;
use core\db\models\item;
/**
 * The modules\defaultModule\controllers\indexController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class indexController extends \zinux\kernel\controller\baseController
{
    /**
    * The modules\defaultModule\controllers\indexController::IndexAction()
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
        $instance = NULL;
        switch(strtoupper($this->request->type))
        {
            case 'HTML':
            case "FOLDERS":
                $instance = new \core\db\models\folder;
                break;
            case "NOTES":
                $instance = new \core\db\models\note;
                break;
            case "LINKS":
                $instance = new \core\db\models\link;
                break;
            default:
                throw new \zinux\kernel\exceptions\invalideArgumentException("Extention `{$this->request->type}` does not supported by explorer....");
        }
        $this->view->items = ($instance->fetchItems($pid, $uid, item::WHATEVER, item::FLAG_UNSET, item::FLAG_UNSET));
        $folder = new \core\db\models\folder;
        $this->view->route = $folder->fetchRouteToRoot($pid, $uid);
    }    

    /**
    * The \modules\defaultModule\controllers\indexController::trashAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function trashesAction()
    {
        if(!\core\db\models\user::IsSignedin()) return;
        $this->layout->AddTitle("Home");
        $f = new \core\db\models\folder();
        if(!isset($this->request->params["directory"]))
            $this->request->params["directory"] = 0;
        $this->view->pid = $pid = $this->request->params["directory"];
        $uid = \core\db\models\user::GetInstance()->user_id;
        $instance = NULL;
        switch(strtoupper($this->request->type))
        {
            case 'HTML':
            case "FOLDERS":
                $instance = new \core\db\models\folder;
                break;
            case "NOTES":
                $instance = new \core\db\models\note;
                break;
            case "LINKS":
                $instance = new \core\db\models\link;
                break;
            default:
                throw new \zinux\kernel\exceptions\invalideArgumentException("Extention `{$this->request->type}` does not supported by explorer....");
        }
        $this->view->items = ($instance->fetchTrashes($uid));
    }

    /**
    * The \modules\defaultModule\controllers\indexController::archiveAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function archivesAction()
    {
        if(!\core\db\models\user::IsSignedin()) return;
        $this->layout->AddTitle("Home");
        $f = new \core\db\models\folder();
        if(!isset($this->request->params["directory"]))
            $this->request->params["directory"] = 0;
        $this->view->pid = $pid = $this->request->params["directory"];
        $uid = \core\db\models\user::GetInstance()->user_id;
        $instance = NULL;
        switch(strtoupper($this->request->type))
        {
            case 'HTML':
            case "FOLDERS":
                $instance = new \core\db\models\folder;
                break;
            case "NOTES":
                $instance = new \core\db\models\note;
                break;
            case "LINKS":
                $instance = new \core\db\models\link;
                break;
            default:
                throw new \zinux\kernel\exceptions\invalideArgumentException("Extention `{$this->request->type}` does not supported by explorer....");
        }
        $this->view->items = ($instance->fetchArchives($uid));
    }

    /**
    * The \modules\defaultModule\controllers\indexController::sharedAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function sharedAction()
    {
        if(!\core\db\models\user::IsSignedin()) return;
        $this->layout->AddTitle("Home");
        $f = new \core\db\models\folder();
        if(!isset($this->request->params["directory"]))
            $this->request->params["directory"] = 0;
        $this->view->pid = $pid = $this->request->params["directory"];
        $uid = \core\db\models\user::GetInstance()->user_id;
        $instance = NULL;
        switch(strtoupper($this->request->type))
        {
            case 'HTML':
            case "FOLDERS":
                $instance = new \core\db\models\folder;
                break;
            case "NOTES":
                $instance = new \core\db\models\note;
                break;
            case "LINKS":
                $instance = new \core\db\models\link;
                break;
            default:
                throw new \zinux\kernel\exceptions\invalideArgumentException("Extention `{$this->request->type}` does not supported by explorer....");
        }
        $this->view->items = ($instance->fetchShared($uid));
    }
}

