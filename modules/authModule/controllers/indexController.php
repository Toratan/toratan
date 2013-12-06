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
        $this->signinAction();
    }

    /**
    * The \modules\authModule\controllers\indexController::signinAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function signinAction()
    {
        # no layout for signin, it has embed layout in it.
        $this->layout->SuppressLayout();#SetLayout("signing");
        # if user already signed in
        if(\core\db\models\user::IsSignedin())
            # abort
            $this->Redirect();
        # set the title
        $this->layout->AddTitle("Sign in toratan");
        # set default continue value
        $this->view->continue = "/";
        # update the continue value if provided
        if(isset($this->request->params["continue"]))
            $this->view->continue = $this->request->params["continue"];
        # if it is GET print the view
        if(!$this->request->IsPOST())
            return;
        # if it is POST?
        # validate the input data
        \zinux\kernel\security\security::IsSecure(
                $this->request->POST,
                array("username", "password")
        );
        # user instance
        $user = new \core\db\models\user;
        # fetch the user
        $fetched_user = $user->Fetch($this->request->params['username'], $this->request->params["password"]);
        # if no user found 
        if(!$fetched_user)
            # promt if
            $this->view->errors[] = "Username/Email or Password didn't match.";
        else
        {
            # if we find the user
            # sign in the user & should we remember the user!?
            $user->Signin($fetched_user, isset($this->request->params["remember-me"]));
            # mission is success 
            # redirect
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
        $sec_cookie = new \zinux\kernel\security\secureCookie;
        $sec_cookie->delete(\zinux\kernel\security\hash::Generate(\core\db\models\user::USER_OBJECT), "/", __SERVER_NAME__);
        $this->Redirect();
    }

    /**
    * The \modules\authModule\controllers\indexController::signupAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function signupAction()
    {
        $this->layout->SetLayout("signing");
        $this->view->username = $this->view->email = "";
        if(\core\db\models\user::IsSignedin())
            $this->Redirect();
        $this->view->layout->AddTitle("Sign up in toratan");
        # set default continue value
        $this->view->continue = "/";
        # update the continue value if provided
        if(isset($this->request->params["continue"]))
            $this->view->continue = $this->request->params["continue"];
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
            try{
                $new_user ->Signup(
                            $this->request->params["username"],
                            $this->request->params["email"],
                            $this->request->params["password"]);
            }
            catch(\core\db\exceptions\alreadyExistsException $aee)
            {
                $this->view->errors[] = "The user already exists!";
                return;
            }
        }
        catch(\core\exceptions\exceptionCollection $ec)
        {
            foreach ($ec->getCollection() as $e)                
                $this->view->errors[] = $e->getMessage();
            $this->view->username = $this->request->params["username"];
            $this->view->email = $this->request->params["email"];
            return;
        }
        
        $this->signinAction();
    }

    /**
    * The \modules\authModule\controllers\indexController::recoveryAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function recoveryAction()
    {
        
    }
}