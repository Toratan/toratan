<?php
namespace modules\authModule\controllers;
require_once 'authController.php';    
/**
 * The modules\authModule\controllers\indexController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class indexController extends authController
{
    /**
    * The modules\authModule\controllers\indexController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
    }

    /**
    * The \modules\authModule\controllers\indexController::signinAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function signinAction()
    {
        
    }

    /**
    * Signouts the user
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function signoutAction()
    {
        $this->Redirect();
    }

    /**
    * The \modules\authModule\controllers\indexController::signupAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function signupAction()
    {
        if(!$this->request->IsPOST())
            return;
        \zinux\kernel\security\security::IsSecure(
                $this->request->params, 
                array("username","email", "password", "conf-password"), 
                array(),
                array('password'=>'conf-password')
        );
        
        try
        {
            $new_user = new \core\db\models\user;
            $new_user -> email = $this->request->params["email"];
            $new_user -> username = $this->request->params["username"];
            $new_user -> password = \zinux\kernel\security\hash::Generate($this->request->params["password"],1);
            $new_user -> user_id = \zinux\kernel\security\hash::Generate($new_user->email);
            $new_user->save();
            $this->Redirect();
            exit;
        }
        catch(Exception $pdoe)
        {
            die("OPS");
        }
    }
}


