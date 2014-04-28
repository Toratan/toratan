<?php
namespace modules\frameModule\controllers;
use core\db\models\item;
defined("FETCH_LIMIT") || define("FETCH_LIMIT", 30);
/**
 * The modules\frameModule\controllers\eController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class eController extends \zinux\kernel\controller\baseController
{
    public function Initiate ()
    {
        parent::Initiate();
        $this->layout->SetLayout("explorer");
        if(!isset($this->request->params["o"]))
            $this->request->params["o"] = 0;
    }
    /**
    * The modules\frameModule\controllers\eController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        if(!\core\db\models\user::IsSignedin()) { trigger_error("UNREGISTERED USERS SHOULD BE ABLE TO VIEW PUBLIC ITESM", E_USER_ERROR); return; }
        $this->layout->AddTitle("Home");
        $f = new \core\db\models\folder();
        if(!isset($this->request->params["directory"]))
            $this->request->params["directory"] = 0;
        $this->view->pid = $pid = $this->request->params["directory"];
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
                $this->request->type = "folders";
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
        $sort_base = "{$instance->WhoAmI()}_title";
        if(isset($this->request->params["sort"])) {
            switch($this->request->params["sort"]) {
                case 2:
                    $sort_base = "updated_at";
                    break;
            }
        }
        $order = "asc";
        if(isset($this->request->params["order"])) {
            switch($this->request->params["order"]) {
                case 1:
                    $order = "desc";
                    break;
            }
        }
        $this->view->is_owner = ($uid == \core\db\models\user::GetInstance()->user_id); 
        $this->view->items = ($instance->fetchItems($uid, $pid, $this->view->is_owner?item::WHATEVER:item::FLAG_SET, item::FLAG_UNSET, item::FLAG_UNSET, array("order" => "$sort_base $order", 'limit' => FETCH_LIMIT, 'offset' => $this->request->params["o"])));
        $old_o = $this->request->params["o"];
        $this->request->params["o"] = intval($this->request->params["o"]) + FETCH_LIMIT;
        $folder = new \core\db\models\folder;
        $this->view->route = $folder->fetchRouteToRoot($pid, $uid);
        if(isset($this->request->params["fetch"])) {
            \zinux\kernel\security\security::ArrayHashCheck($this->request->params, array(session_id()));
            $dt = new \modules\frameModule\models\directoryTree($this->request, \modules\frameModule\models\directoryTree::REGULAR);
            $index = 0;
            $all_count = count($this->view->items);
            foreach ($this->view->items as $item)
            {
                $dt->plotTableRow($item, $item->WhoAmI(), $pid, $this->view->is_owner, (++$index)/$all_count);
            }
            exit;
        }
    }

    /**
    * The \modules\frameModule\controllers\eController::archivesAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function archivesAction()
    {
        if(!\core\db\models\user::IsSignedin()) return;
        $this->layout->AddTitle("Archives");
        $f = new \core\db\models\folder();
        if(!isset($this->request->params["directory"]))
            $this->request->params["directory"] = 0;
        $this->view->pid = $pid = $this->request->params["directory"];
        $uid = \core\db\models\user::GetInstance()->user_id;
        $instance = NULL;
        switch(strtoupper($this->request->type))
        {
            case 'HTML':
                $this->request->type = "folders";
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
        $this->view->is_owner = 1;
    }

    /**
    * The \modules\frameModule\controllers\eController::sharedAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function sharedAction()
    {
        if(!\core\db\models\user::IsSignedin()) return;
        $this->layout->AddTitle("Shared");
        $f = new \core\db\models\folder();
        if(!isset($this->request->params["directory"]))
            $this->request->params["directory"] = 0;
        $this->view->pid = $pid = $this->request->params["directory"];
        $uid = \core\db\models\user::GetInstance()->user_id;
        $instance = NULL;
        switch(strtoupper($this->request->type))
        {
            case 'HTML':
                $this->request->type = "folders";
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
        $this->view->is_owner = 1;
    }

    /**
    * The \modules\frameModule\controllers\eController::trashesAction()
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
        $instance = NULL;
        switch(strtoupper($this->request->type))
        {
            case 'HTML':
                $this->request->type = "folders";
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
        $this->view->is_owner = 1;
    }
}
