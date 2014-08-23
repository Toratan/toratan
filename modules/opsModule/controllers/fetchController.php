<?php
namespace modules\opsModule\controllers;
/**
 * The modules\opsModule\controllers\fetchController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class fetchController extends \zinux\kernel\controller\baseController
{
    public function Initiate() { parent::Initiate(); $this->layout->SuppressLayout();}
    /**
    * The modules\opsModule\controllers\fetchController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction() { throw new \zinux\kernel\exceptions\notFoundException; }
    /**
    * The \modules\opsModule\controllers\fetchController::tagsAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function tagsAction() {
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\accessDeniedException("invalid request type `{$_SERVER["REQUEST_METHOD"]}`");
        \zinux\kernel\security\security::__validate_request($this->request->params);
        \zinux\kernel\security\security::IsSecure($this->request->params, array("term"));
        $this->view->tags = \core\db\models\tag::search_similar($this->request->params["term"]);
        $this->view->origin_term = $this->request->params["term"];
    }
    /**
    * The \modules\opsModule\controllers\fetchController::commentAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function commentAction() {
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\accessDeniedException("invalid request type `{$_SERVER["REQUEST_METHOD"]}`");
        \zinux\kernel\security\security::IsSecure($this->request->params, array("nid", "p", "type"));
        \zinux\kernel\security\security::__validate_request($this->request->params, array($this->request->params["nid"]));
        switch(strtolower($this->request->params["type"])) {
            case "all":
            case "top":
                $func = "__fetch_{$this->request->params["type"]}";
                $fetch_rate = 40;
                $tops =\core\db\models\comment::$func($this->request->params["nid"], ($this->request->params["p"] - 1) * $fetch_rate);
                $is_more = !(count($tops) < $fetch_rate);
                if($is_more) {
                    $is_more = \core\db\models\comment::__fetch_count($this->request->params["nid"]) > $this->request->params["p"] * $fetch_rate;
                }
                $is_owner=!is_null((new \core\db\models\note)->find($this->request->params["nid"],array("conditions"=>array("owner_id = ?", \core\db\models\user::GetInstance()->user_id), "select" => "owner_id")));
                $cr = new \modules\opsModule\models\renderComment($this->request->params["nid"], $is_owner, $tops);
                ob_start();
                    $cr->__render_prev_comments();
                $tops_html =ob_get_clean();
                echo json_encode(array(
                    "count" => count($tops),
                    "comments" => $tops_html,
                    "nextp" => $this->request->params["p"] + 1,
                    "is_more" => $is_more,
                    "func" => "$func"
                ));
                die;
            default:
                throw new \zinux\kernel\exceptions\invalidArgumentException("invalid type `{$this->request->params["type"]}`");
        }
        die;
    }
    /**
    * The \modules\opsModule\controllers\fetchController::popularAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function popularAction()
    {
        \zinux\kernel\security\security::IsSecure($this->request->params, array("type", "id", "uid", "s"), array("type" => function($type){ return in_array(strtolower($type), array("notes"));}));
        \zinux\kernel\security\security::__validate_request($this->request->params, array($this->request->params["type"], $this->request->params["id"], $this->request->params["uid"], $this->request->params["s"]));
        if(!isset($this->request->params["p"]))
            $this->request->params["p"] = 1;
        $s =\core\db\vendors\itemStatus::decode($this->request->params["s"]);
        switch(strtolower($this->request->params["type"])) {
            case "notes":
                $class = "\\core\\db\\models\\".\ActiveRecord\Utils::singularize($this->request->params["type"]);
                $instance = new $class;
                $ps = $instance->fetchPopular($this->request->params["uid"], ($this->request->params["p"] - 1) * 10, 10, $s->is_public, \core\db\models\item::WHATEVER, \core\db\models\item::FLAG_UNSET);
                $o = array();
                foreach($ps as $p) {
                    $i = new \stdClass;
                    $i->id = $p->getItemID();
                    $i->title = $p->getItemTItle();
                    $i->summary = $p->note_summary;
                    $i->owner = new \stdClass;
                    $i->owner->id = $p->owner_id;
                    $i->owner->avatar = \array_slice(\core\ui\html\avatar::get_avatar_link($this->request->params["uid"]), 0, 2);
                    $i->popularity = $p->popularity;
                    $i->created = $p->created_at->format();
                    $i->updated = $p->updated_at->format();
                    $o["items"][] = $i;
                }
                die(json_encode($o));
            default:
                throw new \zinux\kernel\exceptions\invalidArgumentException("Invalid type `{$this->request->params["type"]}`");
        }
        die;
    }
}