<?php
namespace modules\opsModule\controllers;
    
/**
 * The modules\opsModule\controllers\fetchController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class fetchController extends \zinux\kernel\controller\baseController
{
    const STEP_SIZE = 30;
    public function Initiate()
    {
        parent::Initiate();
        # init page count
        if(!isset($this->request->params["page"]))
            $this->request->params["page"] = 1;
        # secure the request
        \zinux\kernel\security\security::IsSecure($this->request->params, array("page", "item"));
        # secure the request item
        if(!in_array($this->request->params["item"], array("folder", "note", "link")))
            throw new \zinux\kernel\exceptions\invalidArgumentException("Undefined `{$this->request->params["item"]}`");
    }
    /**
    * The modules\opsModule\controllers\fetchController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        $class = "\\core\\db\\models\\{$this->request->params["item"]}";
        $instance = new $class;
//        $instance = new \core\db\models\folder;
    }
}
