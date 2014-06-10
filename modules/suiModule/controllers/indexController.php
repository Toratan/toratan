<?php
namespace modules\suiModule\controllers;
use core\db\models\item;
/**
 * The modules\suiModule\controllers\indexController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class indexController extends \zinux\kernel\controller\baseController
{
    public function Initiate()
    {
        parent::Initiate();
        if(isset($this->request->params["ajax"]))
            $this->layout->SuppressLayout();
    }
    /**
    * The modules\suiModule\controllers\indexController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        if(!\core\db\models\user::IsSignedin()) return;
        $this->layout->AddTitle("Home");
        $f = new \core\db\models\folder();
        if(!isset($this->request->params["d"]))
            $this->request->params["d"] = 0;
        $this->view->pid = $pid = $this->request->params["d"];
        $uid = \core\db\models\user::GetInstance()->user_id;
        if(isset($this->request->params["u"]) && $this->request->params["u"] != $uid)
        {
            $target_user = \core\db\models\user::Fetch($this->request->params["u"]);
            if(!$target_user)
                throw new \zinux\kernel\exceptions\notFoundException("No such user exists....");
            $uid = $target_user->user_id;
            $parent = new \core\db\models\folder();
            $parent = $parent->fetch($pid, $uid);
            if(!$parent->is_public)
                throw new \zinux\kernel\exceptions\permissionDeniedException("You don't have permission to view this folder.");
            
        }
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
                throw new \zinux\kernel\exceptions\invalidArgumentException("Extention `{$this->request->type}` does not supported by explorer....");
        }
        $this->view->is_owner = ($uid == \core\db\models\user::GetInstance()->user_id); 
        $this->view->items = ($instance->fetchItems($uid, $pid, $this->view->is_owner?item::WHATEVER:item::FLAG_SET, item::FLAG_UNSET, item::FLAG_UNSET));
        $folder = new \core\db\models\folder;
        $this->view->route = $folder->fetchRouteToRoot($pid, $uid);
    }    

    /**
    * The \modules\suiModule\controllers\indexController::trashAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function trashesAction()
    {
        if(!\core\db\models\user::IsSignedin()) return;
        $this->layout->AddTitle("Home");
        $f = new \core\db\models\folder();
        if(!isset($this->request->params["d"]))
            $this->request->params["d"] = 0;
        $this->view->pid = $pid = $this->request->params["d"];
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
                throw new \zinux\kernel\exceptions\invalidArgumentException("Extention `{$this->request->type}` does not supported by explorer....");
        }
        $this->view->items = ($instance->fetchTrashes($uid));
    }

    /**
    * The \modules\suiModule\controllers\indexController::archiveAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function archivesAction()
    {
        if(!\core\db\models\user::IsSignedin()) return;
        $this->layout->AddTitle("Home");
        $f = new \core\db\models\folder();
        if(!isset($this->request->params["d"]))
            $this->request->params["d"] = 0;
        $this->view->pid = $pid = $this->request->params["d"];
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
                throw new \zinux\kernel\exceptions\invalidArgumentException("Extention `{$this->request->type}` does not supported by explorer....");
        }
        $this->view->items = ($instance->fetchArchives($uid));
    }

    /**
    * The \modules\suiModule\controllers\indexController::sharedAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function sharedAction()
    {
        if(!\core\db\models\user::IsSignedin()) return;
        $this->layout->AddTitle("Home");
        $f = new \core\db\models\folder();
        if(!isset($this->request->params["d"]))
            $this->request->params["d"] = 0;
        $this->view->pid = $pid = $this->request->params["d"];
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
                throw new \zinux\kernel\exceptions\invalidArgumentException("Extention `{$this->request->type}` does not supported by explorer....");
        }
        $this->view->items = ($instance->fetchShared($uid));
    }
}