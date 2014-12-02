<?php
namespace modules\opsModule\controllers;
/**
 * The modules\opsModule\controllers\commentController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class commentController extends \zinux\kernel\controller\baseController
{
    /**
    * The modules\opsModule\controllers\commentController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction() { throw new \zinux\kernel\exceptions\notFoundException; }
    /**
    * The \modules\opsModule\controllers\commentController::newAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function newAction()
    {
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\invalidOperationException("Invalid request type");
        \zinux\kernel\security\security::IsSecure($this->request->params, array("nid", "c"), array("c" => array("is_string", "strlen")));
        \zinux\kernel\security\security::__validate_request($this->request->params, array($this->request->params["nid"]));
        $c = \core\db\models\comment::__new($this->request->params["c"], $this->request->params["nid"], \core\db\models\user::GetInstance()->user_id);
        $note = (new \core\db\models\note)->find(array("conditions" => array("note_id = ?", $this->request->params["nid"]), "select" => "note_id, note_title, owner_id"));
        $is_owner = ($note->owner_id == \core\db\models\user::GetInstance()->user_id);
        if(!$is_owner) {
            $n = new \core\db\models\notification;
            $n->push(\core\db\models\notification::NOTIF_COMMENT, $note, $c);
        }
        $cr =new \modules\opsModule\models\renderComment($this->request->params["nid"], $is_owner, array($c));
        $cr->__render_prev_comments();
        die;
    }
    /**
    * The \modules\opsModule\controllers\commentController::editAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function editAction()
    {
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\invalidOperationException("Invalid request type");
        \zinux\kernel\security\security::IsSecure($this->request->params, array("nid", "cid", "c"), array("c" => array("is_string", "strlen")));
        \zinux\kernel\security\security::__validate_request($this->request->params, array($this->request->params["nid"]));
        $c = \core\db\models\comment::__edit($this->request->params["cid"], $this->request->params["nid"], \core\db\models\user::GetInstance()->user_id, $this->request->params["c"]);
        $cr =new \modules\opsModule\models\renderComment($this->request->params["nid"], true, array($c));
        $cr->__render_prev_comments();
        die;
    }
    /**
    * The \modules\opsModule\controllers\commentController::voteAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function voteAction()
    {
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\invalidOperationException("Invalid request type");
        \zinux\kernel\security\security::IsSecure($this->request->params, array("voteup", "nid", "cid"));
        \zinux\kernel\security\security::__validate_request($this->request->params, array($this->request->params["nid"]));
        $cv = new \core\db\models\comment_voter;
        switch($this->request->params["voteup"]) {
            case -1:
                $cv = $cv->__unvote($this->request->params["cid"], \core\db\models\user::GetInstance()->user_id);
                break;
            case 0:
                $cv = $cv->__vote_down($this->request->params["cid"], \core\db\models\user::GetInstance()->user_id);
                break;
            case 1:
                $cv = $cv->__vote_up($this->request->params["cid"], \core\db\models\user::GetInstance()->user_id);
                break;
            default:
                throw new \zinux\kernel\exceptions\invalidArgumentException("Invalid `voteup` value.");
        }
        echo json_encode(array("vote_up" => @$cv->comment->vote_up, "vote_down" => @$cv->comment->vote_down));
        die;
    }
    /**
    * The \modules\opsModule\controllers\commentController::markAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function markAction()
    {
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\invalidOperationException("Invalid request type");
        \zinux\kernel\security\security::IsSecure($this->request->params, array("nid", "cid", "op"));
        \zinux\kernel\security\security::__validate_request($this->request->params, array($this->request->params["nid"]));
        switch(strtolower($this->request->params["op"])) {
            case "delete":
                $ar =\core\db\models\comment::__delete($this->request->params["nid"], $this->request->params["cid"], \core\db\models\user::GetInstance()->user_id);
                echo json_encode(array("result" => $ar));
                die;
            case "report":
                $is_owner = !is_null((new \core\db\models\note)->find($this->request->params["nid"], array("conditions" => array("owner_id = ?", \core\db\models\user::GetInstance()->user_id), "select" => "owner_id")));
                if(!$is_owner)
                    throw new \zinux\kernel\exceptions\invalidOperationException;
                $ar = \core\db\models\comment::__find($this->request->params["nid"], $this->request->params["cid"]);
                if($ar) 
                    $ar->mark_as_spam();
                echo json_encode(array("result" => $ar ? 1: 0));
                die;
            default:
                throw new \zinux\kernel\exceptions\invalidOperationException("Invalid op-type `{$this->request->params["op"]}`");
        }
        die;
    }
}