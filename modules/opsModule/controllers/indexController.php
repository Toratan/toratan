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
        if(!$this->request->IsPOST()) throw new \zinux\kernel\exceptions\invalidOperationException;
        \zinux\kernel\security\security::IsSecure($this->request->params,
                array("type", "op", "items", "continue"),
                array('is_array' => $this->request->params["items"]));
        \zinux\kernel\security\security::__validate_request($this->request->params,
                array($this->request->params["type"], $this->request->params["continue"], session_id()));
        if(!in_array($this->request->params["type"], array("folder", "note", "link")))
                throw new \zinux\kernel\exceptions\invalidArgumentException("Undefined `{$this->request->params["type"]}`");
        if(!in_array($this->request->params["op"], array("share", "archive", "trash", "restore", "remove")))
                throw new \zinux\kernel\exceptions\invalidArgumentException("Undefined op `{$this->request->params["op"]}`");
        $continue = $this->request->params["continue"];
        $method = $_SERVER['REQUEST_METHOD'];
        $infos = $this->request->params["items"];
        $type = $this->request->params["type"];
        $op = strtolower($this->request->params["op"]);
        $ajax = isset($this->request->params["ajax"]);
        $this->op_index_interface = 1;
        $this->suppress_redirect = 1;
        $counter = 0;
        $uid = \core\db\models\user::GetInstance()->user_id;
        $item_class = "\\core\\db\\models\\$type";
        $ins = new $item_class;
        $shared = 0;
        $unshared = 0;
        $flag = \core\db\models\item::FLAG_UNSET;
        foreach ($infos as $info)
        {
            $item = \modules\opsModule\models\itemInfo::decode($info);
            $func = $op;
            switch($op) {
                case "archive":
                    if(!isset($item->a)) throw new \zinux\kernel\exceptions\invalidOperationException;
                    $flag = $item->a;
                    break;
                case "share":
                    if(!isset($item->s)) throw new \zinux\kernel\exceptions\invalidOperationException;
                    $flag = $item->s;
                    if($flag) $shared++;
                    else $unshared++;
                    break;
                case "trash":
                    $flag = \core\db\models\item::DELETE_PUT_TARSH;
                    goto __OP_FUNC;
                case "restore":
                    $flag = \core\db\models\item::DELETE_RESTORE;
                    goto __OP_FUNC;
                case "remove":
                    $flag = \core\db\models\item::DELETE_PERIOD;
__OP_FUNC:
                    $func = "delete";
                    break;
            }
            $ins->$func($item->i, $uid, $flag);
            $counter++;
        }
        $op_name = "toggle-{$op}d";
        switch($op) {
            case "archive":
                $op_name = (!$flag ? "un-" : "")."archived";
                break;
            case "trash":
                $op_name = "moved to trash";
                break;
            case "remove":
                $op_name = "permanently removed";
                break;
            case "restore":
                $op_name = "restored from trash";
                break;
        }
        $result = "<span class='glyphicon glyphicon-ok'></span> Total<b># $counter <u>$type".($counter>1?"s":"")."</u></b> ha".($counter>1?"ve":"s")." been <b>$op_name</b>.";
        if($op === "share") {
            $result =
                    ($shared ? sprintf("<span class='glyphicon glyphicon-ok'></span> <b>#$shared $type%s</b> ha%s been <b>shared</b>.<br />", ($shared>1?"s":""), ($shared>1?"ve":"s")) : "").
                    ($unshared ? sprintf("<span class='glyphicon glyphicon-ok'></span> <b>#$unshared $type%s</b> ha%s been <b>un-shared</b>.<br />", ($unshared>1?"s":""), ($unshared>1?"ve":"s")) : "");
        }
        if($ajax) {
            echo $result;
            exit;
        }
        $mp = new \core\utiles\messagePipe();
        $mp->write($result);
        header("location: /#!$continue");
        exit;
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
            throw new \zinux\kernel\exceptions\invalidOperationException;
        # the PID is essential to get provided
        \zinux\kernel\security\security::IsSecure(
                $this->request->params,
                array("pid")
        );
        # checking hash-sum with {folder|note|link}.(PID).session_id().user_id
        \zinux\kernel\security\security::__validate_request(
                $this->request->params,
                array($this->request->GetIndexedParam(0), $this->request->params["pid"], session_id(), \core\db\models\user::GetInstance()->user_id));
        if($this->request->params["pid"])
        {
            $parent_folder = new \core\db\models\folder();
            # make sure the parent belongs to current user
            # if not the below method will raise an exception
            $parent_folder->fetch($this->request->params["pid"], \core\db\models\user::GetInstance()->user_id);
        }
        # the error container
        $this->view->errors = array();
        # views values container in case of errors
        $this->view->values = array();
        # default note version
        $note_version = "html";
        $editor_version_id = 0;
        # fetch the user id
        $uid = \core\db\models\user::GetInstance()->user_id;
        # pass PID to the view
        $this->view->pid = $this->request->params["pid"];
        # generate new hash for the view
        $this->view->hash =
                \zinux\kernel\security\security::__get_uri_hash_string(
                        array(
                            $this->request->GetIndexedParam(0),
                            $this->view->pid,
                            session_id(),
                            $uid));
        # if reach here we are OK to proceed the opt
        switch (strtoupper($this->request->GetIndexedParam(0)))
        {
            # the valid 'new' opts are
            case "NOTE":
                if(isset($this->request->params["version"]))
                    $note_version = $this->request->params["version"];
                if($this->request->IsGET()) {
                    if($this->isUsingEditorBuffer())
                        $this->view->edit = 1;
                }
                $folder = new \core\db\models\folder;
                $this->view->route = $folder->fetchRouteToRoot($this->request->params["pid"], \core\db\models\user::GetInstance()->user_id);
                $this->layout->AddTitle("Creating new note");
            case "FOLDER":
            case "LINK":
                # set the proper view
                $this->view->setView("new{$this->request->GetIndexedParam(0)}");
                break;
            default:
                # if no ops matched, raise an exception
                throw new \zinux\kernel\exceptions\invalidOperationException;
        }
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
                        $editor_version_id = 1;
                        break;
                    default:
                        throw new \zinux\kernel\exceptions\invalidArgumentException("Invalid text version!");
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
        if(!($item_value = $this->save_item($item, TRUE, $item_ins, $editor_version_id)))
            return;
        if(isset($this->request->params["ajax"])) {
            $dt = new \modules\frameModule\models\directoryTree($this->request);
            echo $dt->plotTableRow($item_value, strtolower($this->request->GetIndexedParam(0)), $item_value->parent_id, 1);
            exit;
        }
        # otherwise relocate properly
        switch (strtoupper($this->request->GetIndexedParam(0)))
        {
            # the valid 'edit' opts are
            case "FOLDER":
            case "LINK":
                if(!$this->suppress_redirect) {
                    /*
                     * No success notifcation, because the user will see the fucking result by redirecting
                     */
                    # redirect if any redirection provided
                    $this->Redirect();
                    # relocate the browser
                    header("location: /#!/d/{$item_value->parent_id}.{$item}s");
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
                throw new \zinux\kernel\exceptions\invalidOperationException;
        }
        if(!$this->suppress_redirect) {
            # relocate the browser
            header("location: /#!/d/{$this->view->pid}.{$item}s");
            # halt the PHP
            exit;
        }
        if(!$this->ops_index_interface) exit;
    }
    /**
     * An api-access point for {editAction()}
    * @access GET via /ops/editAPI/{folder|note|link}=DIGIT;DIGIT;DIGIT?hash_sum
    * @hash-sum {folder|note|link}.session_id().user_id
    * @by Zinux Generator <b.g.dariush@gmail.com>
     */
    public function editAPIAction() {
        if($this->request->CountIndexedParam() < 2) throw new \zinux\kernel\exceptions\invalidOperationException;
        \zinux\kernel\security\security::__validate_request($this->request->params,
                array($this->request->GetIndexedParam(0), session_id(), \core\db\models\user::GetInstance()->user_id));
        $item = \modules\opsModule\models\itemInfo::decode($this->request->GetIndexedParam(1));
        $this->request->params[$this->request->GetIndexedParam(0)] = $item->i;
        $this->request->params = array_merge($this->request->params,
                \zinux\kernel\security\security::__get_uri_hash_array(
                        array(
                                $this->request->GetIndexedParam(0),
                                $item->i,
                                session_id(),
                                \core\db\models\user::GetInstance()->user_id)));
        $this->request->GenerateIndexedParams();
        $this->editAction();
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
            throw new \zinux\kernel\exceptions\invalidOperationException;
        # checking hash-sum with {folder|note|link}.(ID).session_id().user_id
        \zinux\kernel\security\security::__validate_request(
                $this->request->params,
                array($this->request->GetIndexedParam(0), $this->request->GetIndexedParam(1), session_id(), \core\db\models\user::GetInstance()->user_id));
        $note_version = "html";
        $editor_version_id = 0;
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
                throw new \zinux\kernel\exceptions\invalidOperationException;
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
            $item_value = null;
            if(!($item_value = $this->isUsingEditorBuffer())) {
                $item_value = $item_ins->fetch($this->request->GetIndexedParam(1), \core\db\models\user::GetInstance()->user_id);
                # resore back the values to views
                $this->view->values["{$item}_id"] = $item_value->{"{$item}_id"};
                $this->view->values["{$item}_title"] = $item_value->{"{$item}_title"};
                $this->view->values["{$item}_body"] = $item_value->{"{$item}_body"};
                $this->view->pid = $item_value->parent_id;
                if(isset($item_value->editor_type))
                    $this->view->editor_type = $item_value->editor_type;
            }
            $this->layout->AddTitle("Editing - {$this->view->values["{$item}_title"]}");
            if($item == "note") {
                $folder = new \core\db\models\folder;
                $this->view->route = $folder->fetchRouteToRoot($item_value->parent_id, $item_value->owner_id);
            }
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
                        $editor_version_id = 1;
                        break;
                    default:
                        throw new \zinux\kernel\exceptions\invalidArgumentException("Invalid text version!");
                }
                break;
        }
        # checkpoint for item body and title existance
        \zinux\kernel\security\security::IsSecure($this->request->params, array("{$item}_title", "{$item}_body"));
        # try to save into db
        if(!($item_value = $this->save_item($item, FALSE, $item_ins, $editor_version_id)))
            return;
        # otherwise relocate properly
        switch (strtoupper($this->request->GetIndexedParam(0)))
        {
            # the valid 'edit' opts are
            case "FOLDER":
            case "LINK":
                if(!$this->suppress_redirect) {
                    /*
                     * No success notifcation, because the user will see the fucking result by redirecting
                     */
                    # redirect if any redirection provided
                    $this->Redirect();
                    # relocate the browser
                    header("location: /#!/d/{$item_value->parent_id}.{$item}s");
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
                throw new \zinux\kernel\exceptions\invalidOperationException;
        }
        if(!$this->ops_index_interface) exit;
    }
    /**
     * Saves an item into database
     * @param string $item The item type
     * @param boolean $is_new TRUE if we are adding a NEW item to database; otherwise if we are EDITING pass FALSE
     * @param \core\db\models\item $item_ins The item instance to save
     * @param integer $editor_version_id If target item is a NOTE pass the editor version ID
     * @return mixed if the submission was successfull returns the saved item; otherwise false with errors in {$this->view->errors} container
     */
    protected function save_item($item, $is_new, \core\db\models\item &$item_ins, $editor_version_id) {
        # try to save the item
        try
        {
            /**
             * @var \core\db\models\item
             */
            $item_value = NULL;
            $uid = \core\db\models\user::GetInstance()->user_id;
            switch($item) {
                case "folder":
                    if($is_new) {
                        $item_value = $item_ins->newItem(
                                $this->request->params["{$item}_title"],
                                $this->view->pid,
                                $uid);
                    } else {
                        $item_value = $item_ins->edit(
                                $this->request->GetIndexedParam(1),
                                $uid,
                                $this->request->params["{$item}_title"]);
                    }
                    break;
                case "note":
                    if($is_new) {
                        # we need to pass the edito verion too
                        $item_value = $item_ins->newItem(
                                $this->request->params["{$item}_title"],
                                $this->request->params["{$item}_body"],
                                $this->view->pid,
                                $uid,
                                $editor_version_id);
                    } else  {
                        $nc = \core\db\models\item::NOCHANGE;
                        # we need to pass the editor type as well
                        $item_value = $item_ins->edit(
                                $this->request->GetIndexedParam(1),
                                $uid,
                                $this->request->params["{$item}_title"],
                                $this->request->params["{$item}_body"],
                                $nc, $nc, $nc,
                                $editor_version_id);
                    }
                    if(isset($this->request->params["tagit"])) {
                        \core\db\models\note_tag::tagit_array($item_value, explode(",", $this->request->params["tagit"]));
                    }
                    # generating note's summary
                    $doc = new \DOMDocument();
                    # load a HTML markdown parsed text
                    if(@$doc->loadHTML(\modules\opsModule\models\noteViewModel::__renderText($item_value->getItemBody(), 0))) {
                        # save the processed normalized html body of note
                        $item_value->apply_note_html_body(
                                preg_replace('/^<!DOCTYPE.+?>\\n?/', '', 
                                        str_replace(
                                                array('<html>', '</html>', '<body>', '</body>'), '', $doc->saveHTML()
                                        )
                                ), FALSE);
                        # find paragraph tags
                        $p = $doc->getElementsByTagName("p");
                        /**
                         * Try to locate a good paragraph
                         */
                        $index = 0;
                        $purified_note = "";
                        while($index < $p->length) {
                            $purified_note = @($p->item($index++)->textContent);
                            # only accept a note if it satisfy the 100 min char
                            if(strlen($purified_note) > 100)
                                break;
                            # if the above cond. fails and the $index exceed from $p->length $purified_note 
                            # will contain latest failed $purified_note, it will get currected in next section by 
                            # {$doc->loadHTML($purified_note)} 
                        }
                        $summary = "";
                        if(strlen($purified_note)) {
                            /**
                             * Prepare the purified note to get saved as note's summary
                             */
                            # try to normalize the purified note
                            if(@$doc->loadHTML(trim(substr($purified_note, 0, 597), " .,\t\n\r\0\x0B") . "..."))
                                # fetch the first valid summary
                                $summary = @$doc->getElementsByTagName("p")->item(0)->textContent;
                        }
                        # save the summary into database
                        $item_value->apply_summary($summary);
                    }
                    break;
                case "link":
                    # routine link edit
                    if($is_new) {
                        $item_value = $item_ins->newItem(
                                $this->request->params["{$item}_title"],
                                $this->request->params["{$item}_body"],
                                $this->view->pid,
                                $uid);
                    } else {
                        $item_value = $item_ins->edit(
                                $this->request->GetIndexedParam(1),
                                $uid,
                                $this->request->params["{$item}_title"],
                                $this->request->params["{$item}_body"]);
                    }
                    break;
                default: throw new \zinux\kernel\exceptions\invalidOperationException("Undefined item `$item`");
            }
            return $item_value;
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
            return false;
        }
    }
    /**
    * @access via /ops/view/note/(ID)
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function viewAction()
    {
        # check we have our 2 params loaded
        if($this->request->CountIndexedParam()!=2)
            throw new \zinux\kernel\exceptions\invalidOperationException;
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
                throw new \zinux\kernel\exceptions\invalidOperationException;
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
        $item_value = $item_ins->fetch($this->request->GetIndexedParam(1));
        # check if $uid is the owner?
        $is_owner = ($item_value->owner_id == $uid);
        # if value not found or the current item is not public and the current user is the owner
        if(!$is_owner && (!$item_value->is_public || $item_value->is_trash))
            # drop the balls
            throw new \zinux\kernel\exceptions\accessDeniedException("The `$item` you are looking for does not exists or you don't have the pemission to view it.");
        # pass the item's instance to view
        $this->view->instance = $item_value;
		# fetch route path to current note
        $folder = new \core\db\models\folder;
        $this->view->route = $folder->fetchRouteToRoot($item_value->parent_id, $item_value->owner_id);
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
            throw new \zinux\kernel\exceptions\invalidOperationException;
        # checking hash-sum with {folder|note|link}.(ID).session_id().user_id
        \zinux\kernel\security\security::__validate_request(
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
                throw new \zinux\kernel\exceptions\invalidOperationException;
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
                    throw new \zinux\kernel\exceptions\invalidOperationException;
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
            $mp->write("<span class='glyphicon glyphicon-ok'></span> <b>".($is_trash?"Deleted":"Restored")."</b>");
            if($this->request->params["trash"] == \core\db\models\item::DELETE_PERIOD)
                unset($this->request->params["continue"]);
            # redirect if any redirection provided
            $this->Redirect();
            # otherwise relocate properly
            header("location: /#!/d/{$deleted_item->parent_id}.{$item}s");
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
            throw new \zinux\kernel\exceptions\invalidOperationException;
        # checking hash-sum with {folder|note|link}.(ID).session_id().user_id
        \zinux\kernel\security\security::__validate_request(
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
                throw new \zinux\kernel\exceptions\invalidOperationException;
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
                    throw new \zinux\kernel\exceptions\invalidOperationException;
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
            $mp->write("<span class='glyphicon glyphicon-ok'></span> <b>".($is_archive?"Archived":"Un-Archived")."</b>");
            # redirect if any redirection provided
            $this->Redirect();
            # otherwise relocate properly
            header("location: /#!/d/{$archived_item->parent_id}.{$item}s");
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
            throw new \zinux\kernel\exceptions\invalidOperationException;
        # checking hash-sum with {folder|note|link}.(ID).session_id().user_id
        \zinux\kernel\security\security::__validate_request(
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
                throw new \zinux\kernel\exceptions\invalidOperationException;
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
                    throw new \zinux\kernel\exceptions\invalidOperationException;
            }
        }
        # fetch the items name
        $item = strtolower($this->request->GetIndexedParam(0));
        # generate a proper handler for item creatation
        $item_class = "\\core\\db\\models\\$item";
        # create an instance of item
        $item_ins = new $item_class;
        # share the item
        $item_ins->share($this->request->GetIndexedParam(1), \core\db\models\user::GetInstance()->user_id, $is_share);
        if(!$this->suppress_redirect) {
            # invoke a message pipe line
            $mp = new \core\utiles\messagePipe;
            # indicate the success
            $mp->write("<span class='glyphicon glyphicon-ok'></span> <b>".($is_share?"Shared":"Un-Shared")."</b>");
            # redirect if any redirection provided
            $this->Redirect();
            # otherwise relocate properly
            header("location: /#!/d/{$shared_item->parent_id}.{$item}s");
            exit;
        }
        if(!$this->ops_index_interface) exit;
    }
    /**
    * Subscribe a user to a profile 
    * @access via /ops/follow/u/(ID)/?hash_sum
    * @hash-sum (ID).session_id()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function followAction()
    {
        # we need to have the target user ID
        \zinux\kernel\security\security::IsSecure($this->request->params, array('u'));
        # validate the inputs
        \zinux\kernel\security\security::__validate_request($this->request->params, array($this->request->params['u'], session_id()));
        try
        {
            # subscribe the current user to target  user
            \core\db\models\subscribe::subscribe($this->request->params['u'], \core\db\models\user::GetInstance()->user_id);
        }
        # if already connected?
        # ignore the fact!!!
        catch(\core\db\exceptions\alreadyExistsException $are){ unset($are);}
        # open up a message pipe socket
        $pipe = new \core\utiles\messagePipe;
        # indicate the unsubscription
        $pipe->write("You have successfully <b>subscribed</b>.");
        # relocate the browser
        header("location: /@".\core\db\models\user::Fetch($this->request->params['u'])->username);
        exit;
    }
    /**
    * Unsubscribe a user to a profile 
    * @access via /ops/unfollow/u/(ID)/?hash_sum
    * @hash-sum (ID).session_id()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function unfollowAction()
    {
        # we need to have the target user ID
        \zinux\kernel\security\security::IsSecure($this->request->params, array('u'));
        # validate the inputs
        \zinux\kernel\security\security::__validate_request($this->request->params, array($this->request->params['u'], session_id()));
        # unsubscribe the current user from target user
        \core\db\models\subscribe::unsubscribe($this->request->params["u"], \core\db\models\user::GetInstance()->user_id);
        # open up a message pipe socket
        $pipe = new \core\utiles\messagePipe;
        # indicate the unsubscription
        $pipe->write("You have successfully <b>unsubscribed</b>.");
        # relocate the browser
        header("location: /@".\core\db\models\user::Fetch($this->request->params['u'])->username);
        exit;
    }
    /**
    * @access via /ops/share/link/(ID)/hash_sum
    * @hash-sum \zinux\kernel\security\hash::Generate((ID), 1, 1)
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function gotoAction()
    {
        \zinux\kernel\security\security::IsSecure(
                $this->request->params, 
                array("link"), 
                array(), 
                array("h" => sha1($this->request->params["link"]."LINK")));
        
        $link = new \core\db\models\link;
        $this->view->link = $link->fetch($this->request->params["link"]);
        $this->layout->SuppressLayout();
    }
    /**
     * Validates if we should be using editor buffer for an instance?<br />
     * If so, it also automatically initializes the view's variables according to {new|edit}Action()'s views standard data-format
     * @param string $item_type should be one of {note|folder|link}
     * @return \stdClass|null if fails to use buffer returns NULL otherwise returns an instance of stdClass containing {"{$item_type}_title", "{$item_type}_body", "pid", "owner_id"} attributes
     */
    protected function isUsingEditorBuffer($item_type = "note") {
        switch(strtolower($item_type)) {
            case "note": case "folder": case "link": break;
            default: throw new \zinux\kernel\exceptions\invalidArgumentException("`$item_type` not defined");
        }
        # create a new instance of stdClass
        $value =new \stdClass;
        # open-up a session cache socket with "editor-buffer" ID
        $sc = new \zinux\kernel\caching\sessionCache("editor-buffer");
        # check if anything has been buffered?
        if($sc->isCached("buffer")) {
            # if so?
            # fetch the buffered data
            $b = $sc->fetch("buffer");
            # truncate the buffer
            $sc->delete("buffer");
            # validate the fetched data
            if(!is_array($b) || !\zinux\kernel\security\security::IsSecure($b, array("{$item_type}_title", "{$item_type}_body", "pid", "owner_id"), array(), array(), 0))
                goto __USE_DEFAULT;
            # extract and deploy the fetched data
            $value->{"{$item_type}_id"} = @$b["{$item_type}_id"];
            $value->{"{$item_type}_title"} = $b["{$item_type}_title"];
            $value->{"{$item_type}_body"} = $b["{$item_type}_body"];
            $value->parent_id = $b["pid"];
            $value->owner_id = $b["owner_id"];
            goto __PASS_DATA_2_VALUE;
        }
        # if we reach here it means we failed to fetch any data from buffer
        goto __USE_DEFAULT;
__PASS_DATA_2_VALUE:
        # resore back the values to views
        $this->view->values["{$item_type}_id"] = $value->{"{$item_type}_id"};
        $this->view->values["{$item_type}_title"] = $value->{"{$item_type}_title"};
        $this->view->values["{$item_type}_body"] = $value->{"{$item_type}_body"};
        $this->view->pid = $value->parent_id;
        $this->view->is_using_buffer = true;
        # return extracted values
        return $value;
__USE_DEFAULT:
        $this->view->is_using_buffer = false;
        # we failed!
        return null;
    }
}