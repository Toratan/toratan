<?php
namespace modules\opsModule\controllers;
/**
 * The modules\opsModule\controllers\indexController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class indexController extends \zinux\kernel\controller\baseController
{
    protected $suppress_redirect = 0;
    protected $ops_index_interface = 0;
    public function Initiate()
    {
        parent::Initiate();
        if(array_key_exists("suppress_layout", $this->request->params))
        {
            $this->layout->SuppressLayout ();
            unset($this->request->params["suppress_layout"]);
        }
        if(array_key_exists("suppress_redirection", $this->request->params))
        {
            $this->suppress_redirect = 1;
        }
    }
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
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        # make sure that we get our data from POST
        if(!$this->request->IsPOST()) throw new \zinux\kernel\exceptions\invalideOperationException;
        \zinux\kernel\security\security::IsSecure($this->request->params, 
                array("type", "ops", "items", "continue"),
                array('is_array' => $this->request->params["items"]));
        \zinux\kernel\security\security::ArrayHashCheck($this->request->params, array($this->request->params["type"], $this->request->params["continue"]));
        $continue = $this->request->params["continue"];
        $method = $_SERVER['REQUEST_METHOD'];
        $items = $this->request->params["items"];
        $type = $this->request->params["type"];
        $ops = $this->request->params["ops"];
        $ajax = isset($this->request->params["ajax"]);
        $this->ops_index_interface = 1;
        $this->suppress_redirect = 1;
        $counter = 0;
        switch($ops) {
            case "edit":
                if(count($items) !== 1)
                    throw new \zinux\kernel\exceptions\invalideOperationException("Cannot process multiple items!!");
            case "trash":
            case "remove":
            case "restore":
            case "archive":
            case "share":
                foreach ($items as $item)
                {
                    $params = explode("&",  $type ."&". $item);
                    $this->request->params = array();
                    for($index = 0; $index < count($params); $index ++)
                    {
                        if(preg_match("&(.*)=(.*)&i", $params[$index])) {
                            $sub_param = explode("=", $params[$index]);
                            for($subindex = 0; $subindex < count($sub_param); $subindex += 2) {
                                $this->request->params[$sub_param[$subindex]] = @$sub_param[$subindex + 1];
                            }
                        } else {
                            $this->request->params[$params[$index]] = @!preg_match("&(.*)=(.*)&i", $params[$index + 1]) ? $params[++$index] : NULL;
                        }
                    }
                    $action = $ops;
                    switch ($ops) {
                        case "trash":
                            $this->request->params["trash"] = \core\db\models\item::DELETE_PUT_TARSH;
                            goto __INIT_DELETE_INVOKE;
                        case "remove":
                            $this->request->params["trash"] = \core\db\models\item::DELETE_PERIOD;
                            goto __INIT_DELETE_INVOKE;
                        case "restore":
                            $this->request->params["trash"] = \core\db\models\item::DELETE_RESTORE;
                            goto __INIT_DELETE_INVOKE;
                        __INIT_DELETE_INVOKE:
                            $action = "delete";
                            break;
                        case "edit":
                            # fool the `editAction` to think it is a `GET` request
                            $_SERVER['REQUEST_METHOD'] = "GET";
                            break;
                    }
                    $this->request->params["continue"] = $continue;
                    $this->request->GenerateIndexedParams();
                    $this->{"{$action}Action"}();
                    $_SERVER['REQUEST_METHOD'] = $method;
                    $counter++;
                }
                break;
            default:
                throw new \zinux\kernel\exceptions\invalideOperationException("Invalid operation `{$this->request->params["ops"]}`!!");
        }
        $result = "<b>#$counter $type".($counter>1?"s'":"'s")."</b> status ha".($counter>1?"ve":"s")." been changed <b>successfully</b>!";
        if($ajax) {
            echo $result;
            exit;
        }
        if($action != "edit") {
            $mp = new \core\utiles\messagePipe("ops-index-interface");
            $mp->write($result);
            header("location: $continue");
            exit;
        }
    }
    /**
    * Creates new items
     * @access via /ops/new/{folder|note|link}/pid/(PID)?hash_sum
    * @hash-sum {folder|note|link}.(PID).session_id().user_id
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
        # checking hash-sum with {folder|note|link}.(PID).session_id().user_id
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
        $note_version = "html";
        # if reach here we are OK to proceed the opt
        switch (strtoupper($this->request->GetIndexedParam(0)))
        {
            # the valid 'new' opts are
            case "NOTE":
                if(isset($this->request->params["version"]))
                    $note_version = $this->request->params["version"];
            case "FOLDER":
            case "LINK":
                # set the proper view
                $this->view->setView("new{$this->request->GetIndexedParam(0)}");
                break;
            default:
                # if no ops matched, raise an exception
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
        switch($item)
        {
            case "folder":
                # if it was a folder, define a fake body for it to pass the blow security checkpoint
                $this->request->params["{$item}_body"] = "NULL";
                break;
            case "note":
                switch ($note_version)
                {
                    case "html":
                        break;
                    case "ace":
                        $matches = array();
                        $preg_target = "##(.*)##";
                        $preg_pattern = "/^[\s|\n]*$preg_target/im";
                        $preg_delimiter = "/";
                        if(preg_match($preg_pattern, preg_quote($this->request->params['note_body'], $preg_delimiter), $matches))
                        {
                            $this->request->params["note_title"] = \zinux\kernel\utilities\string::inverse_preg_quote($matches[1]);
                            $this->request->params["note_body"] =
                                    preg_replace("/^([\s|\n]*##{$this->request->params["note_title"]}##)/im", "", $this->request->params["note_body"]);
                        }
                        else
                            $this->request->params["note_title"] = "Untitled Note ......";
                        break;
                    default:
                        throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid text version!");
                }
                break;
        }
        # checkpoint for item body and title existance
        \zinux\kernel\security\security::IsSecure($this->request->params, array("{$item}_title", "{$item}_body"));
        # generate a proper handler for item creatation
        $item_class = "\\core\\db\\models\\$item";
        # invoke an instance of item handler
        $item_ins = new $item_class;
        $item_value = NULL;
        # try adding the item to db
        try
        {
            # if the item was a folder
            if($item == "folder")
                # we don't need to pass the fake body generated above, so we deal with it differently
                $item_value = $item_ins->newItem($this->request->params["{$item}_title"], $this->view->pid, $uid);
            else
                # otherwise we use the same interface for it
                $item_value = $item_ins->newItem($this->request->params["{$item}_title"], $this->request->params["{$item}_body"], $this->view->pid, $uid);
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
        # otherwise relocate properly
        switch (strtoupper($this->request->GetIndexedParam(0)))
        {
            # the valid 'edit' opts are
            case "FOLDER":
            case "LINK":
                if(!$this->suppress_redirect) {
                    # invoke a message pipe line
                    $mp = new \core\utiles\messagePipe;
                    # indicate the success
                    $mp->write("One $item has been <b>created</b> successfully....");
                    # redirect if any redirection provided
                    $this->Redirect();
                    # relocate the browser
                    header("location: /directory/{$item_value->parent_id}.{$item}s");
                    exit;
                }
                break;
            case "NOTE":
                if(!$this->suppress_redirect) {
                    # relocate the browser
                    header("location: /view/note/{$item_value->note_id}");
                    exit;
                }
                break;
            default:
                # if no ops matched, raise an exception
                throw new \zinux\kernel\exceptions\invalideOperationException;
        }
        if(!$this->suppress_redirect) {
            # relocate the browser
            header("location: /directory/{$this->view->pid}.{$item}s");
            # halt the PHP
            exit;
        }
        if(!$this->ops_index_interface) exit;
    }
    /**
    * @access via /ops/edit/{folder|note|link}/(ID)?hash_sum
    * @hash-sum {folder|note|link}.(ID).session_id().user_id
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function editAction()
    {
        # we need at least 2 params to go for
        if($this->request->CountIndexedParam()<2)
            throw new \zinux\kernel\exceptions\invalideOperationException;
        # checking hash-sum with {folder|note|link}.(ID).session_id().user_id
        \zinux\kernel\security\security::ArrayHashCheck(
                $this->request->params,
                array($this->request->GetIndexedParam(0), $this->request->GetIndexedParam(1), session_id(), \core\db\models\user::GetInstance()->user_id));
        $note_version = "html";
        # if reach here we are OK to proceed the opt
        switch (strtoupper($this->request->GetIndexedParam(0)))
        {
            # the valid 'new' opts are
            case "NOTE":
                if(isset($this->request->params["version"]))
                    $note_version = $this->request->params["version"];
                $this->view->edit = 1;
            case "FOLDER":
            case "LINK":
                # set the proper view
                $this->view->setView("new{$this->request->GetIndexedParam(0)}");
                break;
            default:
                # if no ops matched, raise an exception
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
            $this->view->pid = $item_value->parent_id;
            return;
        }
         switch($item)
        {
            case "folder":
                # if it was a folder, define a fake body for it to pass the blow security checkpoint
                $this->request->params["{$item}_body"] = "NULL";
                break;
            case "note":
                switch ($note_version)
                {
                    case "html":
                        break;
                    case "ace":
                        $matches = array();
                        $preg_target = "##(.*)##";
                        $preg_pattern = "/$preg_target/im";
                        $preg_delimiter = "/";
                        if(preg_match($preg_pattern, preg_quote($this->request->params['note_body'], $preg_delimiter), $matches))
                        {
                            $this->request->params["note_title"] = \zinux\kernel\utilities\string::inverse_preg_quote($matches[1]);
                            $this->request->params["note_body"] =
                                    preg_replace("/^([\s|\n]*##{$this->request->params["note_title"]}##)/im", "", $this->request->params["note_body"]);
                        }
                        else
                            $this->request->params["note_title"] = "Untitled Note ......";
                        break;
                    default:
                        throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid text version!");
                }
                break;
        }
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
        # otherwise relocate properly
        switch (strtoupper($this->request->GetIndexedParam(0)))
        {
            # the valid 'edit' opts are
            case "FOLDER":
            case "LINK":
                if(!$this->suppress_redirect) {
                    # invoke a message pipe line
                    $mp = new \core\utiles\messagePipe;
                    # indicate the success
                    $mp->write("One $item has been <b>edited</b> successfully....");
                    # redirect if any redirection provided
                    $this->Redirect();
                    # relocate the browser
                    header("location: /directory/{$item_value->parent_id}.{$item}s");
                    exit;
                }
                break;
            case "NOTE":
                if(!$this->suppress_redirect) {
                    # relocate the browser
                    header("location: /view/note/{$item_value->note_id}");
                    exit;
                }
                break;
            default:
                # if no ops matched, raise an exception
                throw new \zinux\kernel\exceptions\invalideOperationException;
        }
        if(!$this->ops_index_interface) exit;
    }
    /**
    * @access via /ops/view/note/(ID)
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
            # the valid 'view' opts are
            case "NOTE":
                # set the proper view
                $this->view->setView("view{$this->request->GetIndexedParam(0)}");
                break;
            default:
                # if no ops matched, raise an exception
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
    * @access via /ops/delete/{folder|note|link}/(ID)/trash/(-1,0,1)?hash_sum
    * If you set the
    * trash : -1 => retores the item from trash
    * trash : 0  => deletes permanently
    * trash : 1  => logically flag items as trash
    * @hash-sum {folder|note|link}.(ID).session_id().user_id
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function deleteAction()
    {
        # we need at least 2 params to go for
        if($this->request->CountIndexedParam()<2)
            throw new \zinux\kernel\exceptions\invalideOperationException;
        # checking hash-sum with {folder|note|link}.(ID).session_id().user_id
        \zinux\kernel\security\security::ArrayHashCheck(
                $this->request->params,
                array($this->request->GetIndexedParam(0), $this->request->GetIndexedParam(1), session_id(), \core\db\models\user::GetInstance()->user_id));
        # if reach here we are OK to proceed the opt
        switch (strtoupper($this->request->GetIndexedParam(0)))
        {
            # the valid 'delete' opts are
            case "FOLDER":
            case "NOTE":
            case "LINK":
                break;
            default:
                # if no ops matched, raise an exception
                throw new \zinux\kernel\exceptions\invalideOperationException;
        }
        if(!isset($this->request->params["trash"]))
            $is_trash = 0;
        else
        {
            switch ($this->request->params["trash"])
            {
                case \core\db\models\item::DELETE_PERIOD:
                case \core\db\models\item::DELETE_PUT_TARSH:
                case \core\db\models\item::DELETE_RESTORE:
                    $is_trash = $this->request->params["trash"];
                    break;
                default:
                    throw new \zinux\kernel\exceptions\invalideOperationException;
            }
        }
        # fetch the items name
        $item = strtolower($this->request->GetIndexedParam(0));
        # generate a proper handler for item creatation
        $item_class = "\\core\\db\\models\\$item";
        # create an instance of item
        $item_ins = new $item_class;
        # delete the item
        $deleted_item = $item_ins->delete($this->request->GetIndexedParam(1), \core\db\models\user::GetInstance()->user_id, $is_trash);
        if(!$this->suppress_redirect) {
            # invoke a message pipe line
            $mp = new \core\utiles\messagePipe;
            # indicate the success
            $mp->write("One $item has been <b>".($is_trash?"deleted":"restored")."</b> successfully....");                                                                                                                                                                          
            # redirect if any redirection provided
            $this->Redirect();
            # otherwise relocate properly
            header("location: /directory/{$deleted_item->parent_id}.{$item}s");
            exit;
        }
        if(!$this->ops_index_interface) exit;
    }
    /**
    * @access via /ops/archive/{folder|note|link}/(ID)/archive/(0,1)?hash_sum
    * @hash-sum {folder|note|link}.(ID).session_id().user_id
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function archiveAction()
    {
        # we need at least 2 params to go for
        if($this->request->CountIndexedParam()<2)
            throw new \zinux\kernel\exceptions\invalideOperationException;
        \zinux\kernel\utilities\debug::_var(array($this->request->params, $this->request->GetIndexedParam(0), $this->request->GetIndexedParam(1)));
        # checking hash-sum with {folder|note|link}.(ID).session_id().user_id
        \zinux\kernel\security\security::ArrayHashCheck(
                $this->request->params,
                array($this->request->GetIndexedParam(0), $this->request->GetIndexedParam(1), session_id(), \core\db\models\user::GetInstance()->user_id));
        # if reach here we are OK to proceed the opt
        switch (strtoupper($this->request->GetIndexedParam(0)))
        {
            # the valid 'archive' opts are
            case "FOLDER":
            case "NOTE":
            case "LINK":
                break;
            default:
                # if no ops matched, raise an exception
                throw new \zinux\kernel\exceptions\invalideOperationException;
        }
        if(!isset($this->request->params["archive"]))
            $is_archive = 0;
        else
        {
            switch ($this->request->params["archive"])
            {
                case \core\db\models\item::FLAG_SET:
                case \core\db\models\item::FLAG_UNSET:
                    $is_archive = $this->request->params["archive"];
                    break;
                default:
                    throw new \zinux\kernel\exceptions\invalideOperationException;
            }
        }
        # fetch the items name
        $item = strtolower($this->request->GetIndexedParam(0));
        # generate a proper handler for item creatation
        $item_class = "\\core\\db\\models\\$item";
        # create an instance of item
        $item_ins = new $item_class;
        # archive the item
        $archived_item = $item_ins->archive($this->request->GetIndexedParam(1), \core\db\models\user::GetInstance()->user_id, $is_archive);
        if(!$this->suppress_redirect) {
            # invoke a message pipe line
            $mp = new \core\utiles\messagePipe;
            # indicate the success
            $mp->write("One $item has been <b>".($is_archive?"archived":"un-archived")."</b> successfully....");
            # redirect if any redirection provided
            $this->Redirect();
            # otherwise relocate properly
            header("location: /directory/{$archived_item->parent_id}.{$item}s");
            exit;
        }
        if(!$this->ops_index_interface) exit;
    }
    /**
    * @access via /ops/share/{folder|note|link}/(ID)/share/(0,1)?hash_sum
    * @hash-sum {folder|note|link}.(ID).session_id().user_id
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function shareAction()
    {
        # we need at least 2 params to go for
        if($this->request->CountIndexedParam()<2)
            throw new \zinux\kernel\exceptions\invalideOperationException;
        # checking hash-sum with {folder|note|link}.(ID).session_id().user_id
        \zinux\kernel\security\security::ArrayHashCheck(
                $this->request->params,
                array($this->request->GetIndexedParam(0), $this->request->GetIndexedParam(1), session_id(), \core\db\models\user::GetInstance()->user_id));
        # if reach here we are OK to proceed the opt
        switch (strtoupper($this->request->GetIndexedParam(0)))
        {
            # the valid 'share' opts are
            case "FOLDER":
            case "NOTE":
            case "LINK":
                break;
            default:
                # if no ops matched, raise an exception
                throw new \zinux\kernel\exceptions\invalideOperationException;
        }
        if(!isset($this->request->params["share"]))
            $is_share = 0;
        else
        {
            switch ($this->request->params["share"])
            {
                case \core\db\models\item::FLAG_SET:
                case \core\db\models\item::FLAG_UNSET:
                    $is_share = $this->request->params["share"];
                    break;
                default:
                    throw new \zinux\kernel\exceptions\invalideOperationException;
            }
        }
        # fetch the items name
        $item = strtolower($this->request->GetIndexedParam(0));
        # generate a proper handler for item creatation
        $item_class = "\\core\\db\\models\\$item";
        # create an instance of item
        $item_ins = new $item_class;
        # share the item
        $shared_item = $item_ins->share($this->request->GetIndexedParam(1), \core\db\models\user::GetInstance()->user_id, $is_share);
        if(!$this->suppress_redirect) {
            # invoke a message pipe line
            $mp = new \core\utiles\messagePipe;
            # indicate the success
            $mp->write("One $item has been <b>".($is_share?"shared":"un-shared")."</b> successfully....");
            # redirect if any redirection provided
            $this->Redirect();
            # otherwise relocate properly
            header("location: /directory/{$shared_item->parent_id}.{$item}s");
            exit;
        }
        if(!$this->ops_index_interface) exit;
    }
    /**
    * The \modules\opsModule\controllers\indexController::subscribeAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function subscribeAction()
    {
        # validate the inputs
        @\zinux\kernel\security\security::ArrayHashCheck($this->request->params,
            array(\core\db\models\user::GetInstance()->user_id, $this->request->GetIndexedParam(0), session_id()."subscribe"));
        try
        {
            # subscribe the current user to target  user
            \core\db\models\subscribe::subscribe($this->request->GetIndexedParam(0), \core\db\models\user::GetInstance()->user_id);
        }
        # if already connected?
        # ignore the fact!!!
        catch(\core\db\exceptions\alreadyExistsException $are){ unset($are);}
        # open up a message pipe socket
        $pipe = new \core\utiles\messagePipe;
        # indicate the unsubscription
        $pipe->write("You have successfully <b>subscribed</b>.");
        # relocate the browser
        header("location: /profile/{$this->request->GetIndexedParam(0)}");
        exit;
    }
    /**
    * The \modules\opsModule\controllers\indexController::unscribeAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function unsubscribeAction()
    {
        # validate the inputs
        @\zinux\kernel\security\security::ArrayHashCheck($this->request->params,
            array(\core\db\models\user::GetInstance()->user_id, $this->request->GetIndexedParam(0), session_id()."subscribe"));
        # unsubscribe the current user from target user
        \core\db\models\subscribe::unsubscribe($this->request->GetIndexedParam(0), \core\db\models\user::GetInstance()->user_id);
        # open up a message pipe socket
        $pipe = new \core\utiles\messagePipe;
        # indicate the unsubscription
        $pipe->write("You have successfully <b>unsubscribed</b>.");
        # relocate the browser
        header("location: /profile/{$this->request->GetIndexedParam(0)}");
        exit;
    }
    /**
    * The \modules\opsModule\controllers\indexController::gotoAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function gotoAction()
    {
        trigger_error("NOT IMPLEMENTED", E_USER_ERROR);
    }
}