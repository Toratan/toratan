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
     * @access via /ops/new/{folder|note|link}/pid/(PID)?hash_sum
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
        # checking hash-sum with PID.PHPSESSID.user_id
        \zinux\kernel\security\security::ArrayHashCheck(
                $this->request->params,
                array($this->request->GetIndexedParam(0), $this->request->params["pid"], session_id(), \core\db\models\user::GetInstance()->user_id));
        if($this->request->params["pid"])
        {
            $parent_folder = new \core\db\models\folder();
            # make sure the parent belongs to current user
            # if not the below method will raise an exception
            $parent_folder->fetch($this->request->params["pid"], \core\db\models\user::GetInstance()->user_id);
        }
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
        # fetch the user id
        $uid = \core\db\models\user::GetInstance()->user_id;
        # pass PID to the view
        $this->view->pid = $this->request->params["pid"];
        # generate new hash for the view
        $this->view->hash =
                \zinux\kernel\security\security::GetHashString(
                        array(
                            $this->request->GetIndexedParam(0),
                            $this->view->pid,
                            session_id(),
                            $uid));
        # the error container
        $this->view->errors = array();
        # views values container in case of errors
        $this->view->values = array();
        # if it is a get return
        if(!$this->request->IsPOST())
            return;
        # otherwise do the ops
        # fetch the items name
        $item = strtolower($this->request->GetIndexedParam(0));
        if($item == "folder")
            # if it was a folder, define a fake body for it to pass the blow security checkpoint
            $this->request->params["{$item}_body"] = "NULL";
        # checkpoint for item body and title existance
        \zinux\kernel\security\security::IsSecure($this->request->params, array("{$item}_title", "{$item}_body"));
        # generate a proper handler for item creatation
        $item_class = "\\core\\db\\models\\$item";
        # invoke an instance of item handler
        $item_ins = new $item_class;
        # try adding the item to db
        try
        {
            # if the item was a folder
            if($item == "folder")
                # we don't need to pass the fake body generated above, so we deal with it differently
                $item_ins->newItem($this->request->params["{$item}_title"], $this->view->pid, $uid);
            else
                # otherwise we use the same interface for it
                $item_ins->newItem($this->request->params["{$item}_title"], $this->request->params["{$item}_body"], $this->view->pid, $uid);
        }
        # catch any exception raised
        catch(\zinux\kernel\exceptions\appException $e)
        {
            # if it was a collection of exceptions
            if($e instanceof \core\exceptions\exceptionCollection)
                # fetch each of exceptions messages
                foreach($e->getCollection() as $exception)
                    $this->view->errors[] = $exception->getMessage();
            else
                # fetch the message
                $this->view->errors[] = $e->getMessage();
            # resore back the values to views
            $this->view->values["{$item}_title"] = $this->request->params["{$item}_title"];
            $this->view->values["{$item}_body"] = $this->request->params["{$item}_body"];
            # return
            return;
        }
        # relocate the browser
        header("location: /directory/{$this->view->pid}.{$item}s");
        # halt the PHP
        exit;
    }

    /**
    * The \modules\opsModule\controllers\indexController::editAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function editAction()
    {
        # we need at least a param to go for
        if($this->request->CountIndexedParam()<2)
            throw new \zinux\kernel\exceptions\invalideOperationException;
        # checking hash-sum with PID.PHPSESSID.user_id
        \zinux\kernel\security\security::ArrayHashCheck(
                $this->request->params,
                array($this->request->GetIndexedParam(0), $this->request->GetIndexedParam(1), session_id(), \core\db\models\user::GetInstance()->user_id));
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
        # the error container
        $this->view->errors = array();
        # views values container in case of errors
        $this->view->values = array();
        # fetch the items name
        $item = strtolower($this->request->GetIndexedParam(0));
        # generate a proper handler for item creatation
        $item_class = "\\core\\db\\models\\$item";
        # create an instance of item
        $item_ins = new $item_class;
        $item_value = NULL;
        if($this->request->IsGET())
        {
            $item_value = $item_ins->fetch($this->request->GetIndexedParam(1), \core\db\models\user::GetInstance()->user_id);
            # resore back the values to views
            $this->view->values["{$item}_title"] = $item_value->{"{$item}_title"};
            $this->view->values["{$item}_body"] = $item_value->{"{$item}_body"};
            return;
        }
        if($item == "folder")
            # if it was a folder, define a fake body for it to pass the blow security checkpoint
            $this->request->params["{$item}_body"] = "NULL";
        # checkpoint for item body and title existance
        \zinux\kernel\security\security::IsSecure($this->request->params, array("{$item}_title", "{$item}_body"));
        # try adding the item to db
        try
        {
            # if the item was a folder
            if($item == "folder")
                # we don't need to pass the fake body generated above, so we deal with it differently
                $item_value = $item_ins->edit($this->request->GetIndexedParam(1), \core\db\models\user::GetInstance()->user_id, $this->request->params["{$item}_title"]);
            else
                # we don't need to pass the fake body generated above, so we deal with it differently
                $item_value = $item_ins->edit($this->request->GetIndexedParam(1), \core\db\models\user::GetInstance()->user_id, $this->request->params["{$item}_title"], $this->request->params["{$item}_body"]);
        }
        # catch any exception raised
        catch(\zinux\kernel\exceptions\appException $e)
        {
            # if it was a collection of exceptions
            if($e instanceof \core\exceptions\exceptionCollection)
                # fetch each of exceptions messages
                foreach($e->getCollection() as $exception)
                    $this->view->errors[] = $exception->getMessage();
            else
                # fetch the message
                $this->view->errors[] = $e->getMessage();
            # resore back the values to views
            $this->view->values["{$item}_title"] = $this->request->params["{$item}_title"];
            $this->view->values["{$item}_body"] = $this->request->params["{$item}_body"];
            # return
            return;
        }
        # relocate the browser
        header("location: /directory/{$item_value->parent_id}.{$item}s");
        exit;
    }

    /**
    * Views content of items
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function viewAction()
    {
        # check we have our 2 params loaded
        if($this->request->CountIndexedParam()!=2)
            throw new \zinux\kernel\exceptions\invalideOperationException;
        # if reach here we are OK to proceed the opt
        switch (strtoupper($this->request->GetIndexedParam(0)))
        {
            # the valid 'NEW' opts are
            case "NOTE":
                # set the proper view
                $this->view->setView("view{$this->request->GetIndexedParam(0)}");
                break;
            default:
                # if no NEW opt matched, raise an exception
                throw new \zinux\kernel\exceptions\invalideOperationException;
        }
        # fetch the user id
        $uid = (\core\db\models\user::IsSignedin()?\core\db\models\user::GetInstance()->user_id:NULL);
        # the error container
        $this->view->errors = array();
        # otherwise do the ops
        # fetch the items name
        $item = strtolower($this->request->GetIndexedParam(0));
        # generate a proper handler for item creatation
        $item_class = "\\core\\db\\models\\$item";
        # invoke an instance of item handler
        $item_ins = new $item_class;
        # try fetch from db
        $item_value = NULL;
        try
        {
            $item_value = $item_ins->fetch($this->request->GetIndexedParam(1));
        }
        catch(\Exception $error_happened){ if(!preg_match("#Couldn't find (.*) with ID=#i", $error_happened->getMessage())) throw $error_happened; }
        # if value not found or the current item is not public and the current user is the owner 
        if(isset($error_happened) || !$item_value || (!$item_value->is_public && (!$uid || $item_value->owner_id != $uid)))
            # drop the balls
            throw new \zinux\kernel\exceptions\notFoundException("The `$item` you are looking for does not exists or you don't have the pemission to view it.");
        # pass the item's instance to view
        $this->view->instance = $item_value;
    }

    /**
    * The \modules\opsModule\controllers\indexController::deleteAction()
    * @access via /ops/delete/{folder|note|link}/(ID)?hash_sum
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function deleteAction()
    {
        # we need at least a param to go for
        if($this->request->CountIndexedParam()<2)
            throw new \zinux\kernel\exceptions\invalideOperationException;
        # checking hash-sum with PID.PHPSESSID.user_id
        \zinux\kernel\security\security::ArrayHashCheck(
                $this->request->params,
                array($this->request->GetIndexedParam(0), $this->request->GetIndexedParam(1), session_id(), \core\db\models\user::GetInstance()->user_id));
        # if reach here we are OK to proceed the opt
        switch (strtoupper($this->request->GetIndexedParam(0)))
        {
            # the valid 'NEW' opts are
            case "FOLDER":
            case "NOTE":
            case "LINK":
                break;
            default:
                # if no NEW opt matched, raise an exception
                throw new \zinux\kernel\exceptions\invalideOperationException;
        }
        $is_trash = isset($this->request->params["trash"]) && $this->request->params["trash"];
        # fetch the items name
        $item = strtolower($this->request->GetIndexedParam(0));
        # generate a proper handler for item creatation
        $item_class = "\\core\\db\\models\\$item";
        # create an instance of item
        $item_ins = new $item_class;
        # delete the item
        $deleted_item = $item_ins->delete($this->request->GetIndexedParam(1), \core\db\models\user::GetInstance()->user_id, $is_trash);
        # relocate the browser
        header("location: /directory/{$deleted_item->parent_id}.{$item}s");
        exit;
    }
}

