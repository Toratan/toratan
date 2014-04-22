<?php
namespace modules\frameModule\controllers;

/**
 * The modules\frameModule\controllers\eController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class eController extends \zinux\kernel\controller\baseController
{
    /**
    * The modules\frameModule\controllers\eController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        $target = "root";
        if($this->request->CountIndexedParam()) $target = $this->request->GetIndexedParam(0);
        switch($target) {
            default:
                die("Undefined `{$target}`");
        }
    }
}
