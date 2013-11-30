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
        if(\core\db\models\user::IsSignedin())
            $this->Redirect();
        $this->layout->AddTitle("Signin....");
        $this->view->continue = "/";
        if(isset($this->request->params["continue"]))
            $this->view->continue = $this->request->params["continue"];
        
        if(!$this->request->IsPOST())
            return;
        
        \zinux\kernel\security\security::IsSecure(
                $this->request->POST,
                array("username", "password")
        );
        
        $user = new \core\db\models\user;
        
        $fetched_user = $user->Fetch($this->request->params['username'], $this->request->params["password"]);
        
        if(!$fetched_user)
            $this->view->errors[] = "username/email didn't match.";
        else
        {
            $user->Signin($fetched_user);
            $this->Redirect();
        }
    }

    /**
    * Signouts the user
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function signoutAction()
    {
        $this->layout->AddTitle("Signing out....");
        \core\db\models\user::Signout();
        $this->Redirect();
    }

    /**
    * The \modules\authModule\controllers\indexController::signupAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function signupAction()
    {
        if(\core\db\models\user::IsSignedin())
            $this->Redirect();
        $this->view->layout->AddTitle("Signup....");
        if(!$this->request->IsPOST())
            return;
        $valid = \zinux\kernel\security\security::IsSecure(
                $this->request->POST, 
                array("username","email", "password", "conf-password"), 
                array(),
                array('password'=>$this->request->POST['conf-password']), 0, 0
        );
        if(!$valid)
        {
            $this->view->errors[] = "All fields are required and password should match!";
            return;
        }
        $new_user = new \core\db\models\user;
        try
        {
            $new_user ->Signup(
                        $this->request->params["username"],
                        $this->request->params["email"],
                        $this->request->params["password"]);
        }
        catch(\core\exceptions\exceptionCollection $ec)
        {
            foreach ($ec->getCollection() as $e)                
                $this->view->errors[] = $e->getMessage();
            return;
        }
        
        $this->signinAction();
    }
}