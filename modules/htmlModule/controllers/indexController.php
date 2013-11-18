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
        if(array_key_exists("clear_session",$this->request->params))
        {
            echo "SESSION CLEARED";
            session_destroy();
            unset($_SESSION);
        }
        $this->layout->AddTitle("Home");
        $this->view->users = \core\db\models\user::all();
        if(array_key_exists("show_session",$this->request->params))
        {
            if(\core\db\models\user::IsSignedin())
            {
                echo "YOU ARE SIGNED IN";
                \zinux\kernel\utilities\debug::_var(\core\db\models\user::GetInstance());
            }
            \zinux\kernel\utilities\debug::_var($_SESSION);
        }
        $f = new \core\db\models\folder();
        #$f->addAnItem();
        echo "<div>";
        echo "ALL {$f->whoami()} count : {$f->count()}";
        echo "</div>";
#        \zinux\kernel\utilities\debug::_var(\zinux\kernel\caching\fileCache::getInternalCaches());
    }
}
