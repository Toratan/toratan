<?php
namespace modules\frameModule\controllers;
use core\db\models\item;
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
        $this->view->target = "root";
        if($this->request->CountIndexedParam()) $this->view->target = $this->request->GetIndexedParam(0);
        switch($this->view->target) {
            default:
                //die("Undefined `{$target}`");
        }
        \zinux\kernel\utilities\debug::_var($this->request->GetURI());
    }

    /**
    * The \modules\frameModule\controllers\eController::archivesAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function archivesAction()
    {
        
    }

    /**
    * The \modules\frameModule\controllers\eController::sharedAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function sharedAction()
    {
        
    }

    /**
    * The \modules\frameModule\controllers\eController::trashesAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function trashesAction()
    {
        
    }
}
