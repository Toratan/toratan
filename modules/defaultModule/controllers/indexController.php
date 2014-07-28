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
        $tag = \core\db\models\tag::search($this->request->GetIndexedParam(0));
        if(!$tag)
            throw new \zinux\kernel\exceptions\notFoundException("The tag `{$this->request->GetIndexedParam(0)}` not found");
//    \zinux\kernel\utilities\debug::_var($tag->fetch_related_notes(0), 1);
    }
}
