<?php
namespace modules\authModule\controllers;
require_once 'authController.php';    
/**
 * The modules\authModule\controllers\indexController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class indexController extends authController
{
    public function Initiate()
    {
        parent::Initiate();
        if(array_key_exists("suppress_layout", $this->request->params))
        {
            $this->layout->SuppressLayout ();
            unset($this->request->params["suppress_layout"]);
        }
    }
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
    public function signinAction($oauth2callback = 0)
    {
        # no layout for signin, it has embed layout in it.
        $this->layout->SetLayout("signin");
        # set title
        $this->layout->AddTitle("Sign in toratan");
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
        if(!$oauth2callback && !$this->request->IsPOST())
            return;
        # if it is POST?
        # validate the input data
        \zinux\kernel\security\security::IsSecure(
                $this->request->params,
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
    public function signupAction($auto_sign_in = 1, $oauth2callback = 0)
    {
        $this->layout->SetLayout("signup");
        $this->view->username = $this->view->email = "";
        if(\core\db\models\user::IsSignedin())
            $this->Redirect();
        $this->view->layout->AddTitle("Sign up in toratan");
        # set default continue value
        $this->view->continue = "/";
        # update the continue value  if provided
        if(isset($this->request->params["continue"]))
            $this->view->continue = $this->request->params["continue"];
        if(!$oauth2callback && !$this->request->IsPOST())
            return;
        $valid = \zinux\kernel\security\security::IsSecure(
                $this->request->params, 
                array("username","email", "password", "conf-password"), 
                array(),
                array('password'=>@$this->request->params['conf-password']), 0, 0
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
                unset($aee);
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
        # flag the redirection to profile creation
        $this->request->params["continue"] = "/profile/edit";
        # if auto signin?
        if($auto_sign_in)
            # do signin
            $this->signinAction();
        else
            return  $new_user;
    }

    /**
    * The \modules\authModule\controllers\indexController::recoveryAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function recoveryAction()
    {
        $this->layout->SetLayout("signin");
        $this->layout->AddTitle("Account Recovery...");
        if(!$this->request->IsPOST())
            return;
        \zinux\kernel\security\security::IsSecure($this->request->params, array("email"));
        \zinux\kernel\security\security::__validate_request($this->request->params, array(session_id(), "r3c0veRI"));
        # we are all good
        if(!($fetched_user = (new \core\db\models\user)->Fetch($this->request->params["email"]))) { $this->view->errors[] = "The email not registered ..."; return; }
        # validate recaptcha
        if(!(new \vendor\recaptcha\recaptcha)->is_recaptcha_valid()) { $this->view->errors[] = "Invalid recaptcha!"; return; }
        # factor an instance of php mailer
        $mail = new \core\utiles\Mailer("noreply", \zinux\kernel\application\config::GetConfig("toratan.mail.noreply.password"));
        # add a subject
        $mail->Subject = "Password Reset";
        # add the reciever address
        $mail->addAddress($this->request->params["email"]);
        # start reading the html context of reset mail
        ob_start();
            $this->view->RenderPartial("recover_passwd_reset", array("user" => $fetched_user));
        # set the html msg and clean the ob's buffer
        $mail->msgHTML(ob_get_clean());
        # msgHTML also sets AltBody, but if you want a custom one, set it afterwards
        $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
        # add the reciever address
        $mail->addAddress($this->request->params["email"]);
        # try to send the email
        if (!$mail->send()) 
            throw new \RuntimeException("Counld'n send email to `{$this->request->params["email"]}` due to error : `{$mail->ErrorInfo}`");  
        # open up a message pipe
        $mp = new \core\utiles\messagePipe("recovery");
        # purge the message, with 60 second expiration time
        $mp->write("The recovery link has been sent to your email address...", 60);
    }

    /**
    * The \modules\authModule\controllers\indexController::oauth2callbackAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function oauth2callbackAction()
    {
        if(!isset($_GET["code"]) || empty($_GET['code'])) { header("location: /signin"); exit; }
        $gauth = new \modules\authModule\models\gAuth();
        
        $gauth->authenticate($_GET['code']);
        
        if(!$gauth->getInfo())
            throw new \zinux\kernel\exceptions\accessDeniedException("Couldn't get the code from google auth!");
        
        $info = $gauth->getInfo();
        
        if(!isset($info->email))
            throw new \zinux\kernel\exceptions\invalidOperationException("No email address provided!!");                
        
        $user_id = \core\db\models\user::Fetch($info->email);
        
        if ($user_id === null)
        {

            $this->request->params["username"] = \preg_replace("#([^a-z0-9])*#i", "", $info->email);
            $this->request->params["email"] = $info->email;
            $this->request->params["password"] = $info->id;
            $this->request->params["conf-password"] = $info->id;
            $this->signupAction(0, 1);
__CHECK_ERROR:
            if(@count($this->view->errors))
            {
                $this->view->suppressView(0);
                $this->layout->SetLayout("signin");
                $this->view->setView("signin");
                return;
            }
            $user_id = \core\db\models\user::Fetch($this->request->params["username"]);
            if(!$user_id)
            {
                $this->view->errors[] = "Something went wrong!";
                /**
                 * This is a serious bug, alert the owner
                 */
                goto __CHECK_ERROR;
            }
            $profile = \core\db\models\profile::getInstance($user_id->user_id);
            $profile->first_name = $info->givenName;
            $profile->last_name = $info->familyName;
            if(@$info->gender)
                $profile->is_male = \strtolower($info->gender) == "male" ? 1 : 0;
            if(isset($info->picture))
            {
                # get complete path of origin image and its thumbnail path
                list($orig_path, $thum_path) = \modules\opsModule\models\avatarPathName::generate($info->picture, $profile);
                # download the picture onto the disk
                if(!file_put_contents($orig_path, file_get_contents($info->picture)))
                    throw new \core\exceptions\uploadException(UPLOAD_ERR_CANT_WRITE);
                # set origin path in profile
                \modules\opsModule\models\avatarPathName::setProfile($profile, "/$orig_path");
                # create a thumbnail for original image
                if(!@\core\ui\html\avatar::make_thumbnail($orig_path, $thum_path))
                    throw new \zinux\kernel\exceptions\invalidOperationException("File uploaded but unable to create thumbnail!");
                # set thumbnail path in profile
                \modules\opsModule\models\avatarPathName::setProfile($profile, NULL, "/$thum_path");
            }
            $profile->save();
            # flag the redirection to profile creation
            $this->request->params["continue"] = "/profile/edit";
            $this->signinAction(1);
        } else {
            $this->request->params["continue"] = "/";
            $user_id->Signin($user_id, 1);
        }
        $this->Redirect();
        exit;
    }

    /**
    * The \modules\authModule\controllers\indexController::recovery_resetAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function recovery_resetAction()
    {
        if(!$this->request->CountIndexedParam() != 2)
            throw new \zinux\kernel\exceptions\accessDeniedException;
        if($this->request->GetIndexedParam(1) !== \zinux\kernel\security\hash::Generate($this->request->GetIndexedParam(0), 1, 1))
                throw new \zinux\kernel\exceptions\invalidOperationException;
        $user = \core\db\models\password_reset::__get_user_from_request_id($this->request->GetIndexedParam(0), 0);
        \zinux\kernel\utilities\debug::_var(array($this->request->params, $user), 1);
    }
}