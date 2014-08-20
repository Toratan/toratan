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
    }
    /**
    * The \modules\opsModule\controllers\commentController::editAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function editAction()
    {
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
    }
}