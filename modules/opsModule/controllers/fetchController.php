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
        \zinux\kernel\utilities\debug::_var($this->request->params, 1);
    }
}
