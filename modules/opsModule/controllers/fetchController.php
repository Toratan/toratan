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
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\accessDeniedException("invalid request type `{$_SERVER["REQUEST_METHOD"]}`");
        \zinux\kernel\security\security::__validate_request($this->request->params);
        \zinux\kernel\security\security::IsSecure($this->request->params, array("term"));
        $this->view->tags = \core\db\models\tag::search_similar($this->request->params["term"]);
        $this->view->origin_term = $this->request->params["term"];
        $this->layout->SuppressLayout();
    }
}
