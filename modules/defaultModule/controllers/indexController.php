<?php
namespace modules\defaultModule\controllers;
    
/**
 * The modules\defaultModule\controllers\indexController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class indexController extends \zinux\kernel\controller\baseController
{
    /**
    * The modules\defaultModule\controllers\indexController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
    }

    /**
    * The \modules\defaultModule\controllers\indexController::tagAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function tagAction()
    {
        $this->layout->SetLayout("basic");
        $this->layout->AddTitle("Tag Browser");
        if(!$this->request->CountIndexedParam())
            throw new \zinux\kernel\exceptions\invalidArgumentException("No tag value passed");
        \zinux\kernel\security\security::IsSecure($this->request->params, array("list"));
        $this->view->total_count = 0;
        $this->view->is_more = false;
        $this->view->notes = array();
        foreach (array("page") as $arg) {
            if(!isset($this->request->params[$arg]) || !is_numeric($this->request->params[$arg]) || $this->request->params[$arg] < 1 )
                $this->request->params[$arg] = 1;
        }
        $tag = \core\db\models\tag::search($this->request->params["list"]);
        if(!$tag)
            return;
        $order = "popularity DESC";
        switch(@strtolower($this->request->params["order"])){
            default:
            case "popularity":
                $this->request->params["order"] = 1;
                break;
            case "new":
                $order = "created_at DESC";
                $this->request->params["order"] = 2;
                break;
        }
        list($this->view->total_count, $this->view->notes) = $tag->fetch_related_notes(($this->request->params["page"] - 1) * 10, $order);
        $this->view->is_more = ($this->request->params["page"] * 10 < $this->view->total_count);
    }
}
