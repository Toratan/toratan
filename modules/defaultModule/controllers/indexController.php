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
        if(!$this->request->CountIndexedParam())
            throw new \zinux\kernel\exceptions\invalidArgumentException("No tag value passed");
        \zinux\kernel\security\security::IsSecure($this->request->params, array("list"));
        $tag = \core\db\models\tag::search($this->request->params["list"]);
        if(!$tag)
            throw new \zinux\kernel\exceptions\notFoundException("The tag `{$this->request->params["list"]}` not found");
        if(!isset($this->request->params["page"]) || !is_numeric($this->request->params["page"]) || $this->request->params["page"] < 1 )
            $this->request->params["page"] = 1;
        $this->layout->SetLayout("basic");
        list($this->view->total_count, $this->view->notes) = $tag->fetch_related_notes(($this->request->params["page"] - 1) * 10);
        $this->view->is_more = ($this->request->params["page"] * 10 < $this->view->total_count);
    }
}
