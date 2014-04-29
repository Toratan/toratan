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
        if(!isset($this->request->params["l"]))
            $this->request->params["l"] = FETCH_LIMIT;
        
                
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
        $this->view->is_owner = ($uid == \core\db\models\user::GetInstance()->user_id); 
        $this->executeQuery("fetchItems",  
                \modules\frameModule\models\directoryTree::REGULAR,
                array($uid, $pid, $this->view->is_owner?item::WHATEVER:item::FLAG_SET, item::FLAG_UNSET, item::FLAG_UNSET));
        $folder = new \core\db\models\folder;
        $this->view->route = $folder->fetchRouteToRoot($pid, $uid);
    }
    protected function executeQuery($func, $dtmode, array $args) {
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
        $args[] = array("order" => "$sort_base $order", 'limit' => $this->request->params["l"], 'offset' => $this->request->params["o"]);
        $this->view->items = call_user_func_array(array($instance, $func), $args);
        if(isset($this->request->params["fetch"])) {
            \zinux\kernel\security\security::ArrayHashCheck($this->request->params, array(session_id()));
            $dt = new \modules\frameModule\models\directoryTree($this->request, $dtmode);
            $index = 0;
            $all_count = count($this->view->items);
            foreach ($this->view->items as $item)
            {
                $dt->plotTableRow($item, $item->WhoAmI(), $item->parent_id, $this->view->is_owner, (++$index)/$all_count);
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
        if(!isset($this->request->params["d"]))
            $this->request->params["d"] = 0;
        $this->view->pid = $pid = $this->request->params["d"];
        $this->view->is_owner = 1;
        $this->executeQuery("fetchArchives",  \modules\frameModule\models\directoryTree::SHARED, array(\core\db\models\user::GetInstance()->user_id));
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
        if(!isset($this->request->params["d"]))
            $this->request->params["d"] = 0;
        $this->view->pid = $pid = $this->request->params["d"];
        $this->view->is_owner = 1;
        $this->executeQuery("fetchShared",  \modules\frameModule\models\directoryTree::ARCHIVE, array(\core\db\models\user::GetInstance()->user_id));
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
        if(!isset($this->request->params["d"]))
            $this->request->params["d"] = 0;
        $this->view->pid = $pid = $this->request->params["d"];
        $this->view->is_owner = 1;
        $this->executeQuery("fetchTrashes",  \modules\frameModule\models\directoryTree::TRASH, array(\core\db\models\user::GetInstance()->user_id));
    }
}
