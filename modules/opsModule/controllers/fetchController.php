<?php
namespace modules\opsModule\controllers;
    
/**
 * The modules\opsModule\controllers\fetchController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class fetchController extends \zinux\kernel\controller\baseController
{
    /**
    * The modules\opsModule\controllers\fetchController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction() { throw new \zinux\kernel\exceptions\notImplementedException; }

    /**
    * The \modules\opsModule\controllers\fetchController::tagsAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function tagsAction() {
        \zinux\kernel\security\security::__validate_request($this->request->params);
        \zinux\kernel\security\security::IsSecure($this->request->params, array("term"));
        $this->view->tags = \core\db\models\tag::search($this->request->params["term"]);
        $this->view->origin_term = $this->request->params["term"];
        die;
    }
}
