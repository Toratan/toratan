<?php
namespace modules\opsModule\controllers;
    
/**
 * The modules\opsModule\controllers\indexController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class indexController extends \zinux\kernel\controller\baseController
{
    public function Initiate() 
    {
        parent::Initiate();
        if(array_key_exists("suppress_layout", $this->request->params))
        {
            $this->layout->SuppressLayout ();
            unset($this->request->params["suppress_layout"]);
        }
    }
    /**
    * The modules\opsModule\controllers\indexController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        throw new \zinux\kernel\exceptions\invalideOperationException;
    }

    /**
    * Creates new items
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function newAction()
    {
        # we need at least a param to go for
        if(!count($this->request->params))
            throw new \zinux\kernel\exceptions\invalideOperationException;
        # the PID is essential to get provided
        \zinux\kernel\security\security::IsSecure(
                $this->request->params,
                array("pid")
        );
        # checking hash-sum with PID.PHPSESSID.USER_ID
        \zinux\kernel\security\security::ArrayHashCheck(
                $this->request->params, 
                array($this->request->GetIndexedParam(0), $this->request->params["pid"], session_id(), \core\db\models\user::GetInstance()->user_id));
        # if reach here we are OK to proceed the opt
        switch (strtoupper($this->request->GetIndexedParam(0)))
        {
            # the valid 'NEW' opts are
            case "FOLDER":
            case "NOTE":
            case "LINK":
                # set the proper view
                $this->view->setView("new{$this->request->GetIndexedParam(0)}");
                break;
            default:
                # if no NEW opt matched, raise an exception
                throw new \zinux\kernel\exceptions\invalideOperationException;
        }
        # generate new hash for the view
        $this->view->hash = 
                \zinux\kernel\security\security::GetHashString(
                        array(
                            $this->request->GetIndexedParam(0),
                            $this->request->params["pid"], 
                            session_id(), 
                            \core\db\models\user::GetInstance()->user_id));
        # pass PID to the view
        $this->view->pid = $this->request->params["pid"];
        # the error container
        $this->view->errors = array();
        # views values container in case of errors
        $this->view->values = array();
        # if it is a get return
        if(!$this->request->IsPOST())
            return;
        # otherwise do the ops
        switch(strtoupper($this->request->GetIndexedParam(0)))
        {
            case "NOTE":
            case "FOLDER":
            case "LINK":
                $item = strtolower($this->request->GetIndexedParam(0));
                if($item == "folder")
                    $this->request->params["{$item}_body"] = "NULL";
                \zinux\kernel\security\security::IsSecure($this->request->params, array("{$item}_title", "{$item}_body"));
                $uid = \core\db\models\user::GetInstance()->user_id;
                $item_class = "\\core\\db\\models\\$item";
                $item_ins = new $item_class;
                try
                {
                    if($item == "folder")
                        $item_ins->newItem($this->request->params["{$item}_title"], $this->view->pid, $uid);
                    else
                        $item_ins->newItem($this->request->params["{$item}_title"], $this->request->params["{$item}_body"], $this->view->pid, $uid);
                }
                catch(\zinux\kernel\exceptions\appException $e)
                {
                    $this->view->errors[] = $e->getMessage();
                    $this->view->values["{$item}_title"] = $this->request->params["{$item}_title"];
                    $this->view->values["{$item}_body"] = $this->request->params["{$item}_body"];
                    return;
                }
                header("location: /directory/{$this->view->pid}.folders");
                exit;
        }
    }

    /**
    * The \modules\opsModule\controllers\indexController::editAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function editAction()
    {
        
    }

    /**
    * The \modules\opsModule\controllers\indexController::viewAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function viewAction()
    {
        
    }

    /**
    * The \modules\opsModule\controllers\indexController::deleteAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function deleteAction()
    {
        
    }
}

