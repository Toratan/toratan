<?php
namespace modules\htmlModule\controllers;
    
/**
 * The modules\htmlModule\controllers\indexController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class indexController extends \zinux\kernel\controller\baseController
{
    /**
    * The modules\htmlModule\controllers\indexController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        $this->view->users = \core\db\models\user::all();
        if(\core\db\models\user::IsSignedin())
        {
            echo "YOU ARE SIGNED IN";
            \zinux\kernel\utilities\debug::_var(\core\db\models\user::GetInstance());
        }
            \zinux\kernel\utilities\debug::_var($_SESSION);
    }
}
