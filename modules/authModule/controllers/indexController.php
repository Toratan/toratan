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
    public function signinAction()
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
        if(!$this->request->IsPOST())
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
    public function signupAction($auto_sign_in = 1)
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
        if(!$this->request->IsPOST())
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
        $user = new \core\db\models\user;
        $fetched_user = $user->Fetch($this->request->params["email"]);
        if(!$fetched_user)
        {
            $this->view->errors[] = "The email not registered ...";
            return;
        }
        /**
         * Send a recovery email here
         */
        /**
         * indicate the recovery has been sent
         */
        # open up a message pipe
        $mp = new \core\utiles\messagePipe("recovery");
        # purge the message, with 60 second expiration time
        $mp->write("The recovery link has been sent to your email address...", 60);
    }

    /**
    * The \modules\authModule\controllers\indexController::remote_conAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function remote_conAction()
    {
        $this->view->suppressView();
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\invalidOperationException;
        \zinux\kernel\security\security::IsSecure($_POST, array("oa_action", "oa_social_login_token", "connection_token"));
        //Get connection_token
        $token = $_POST['connection_token'];

        //Your Site Settings
        $site_subdomain = 'toratan';
        $site_public_key = '1162108d-e631-4dce-a593-3cd6ba3d97af';
        $site_private_key = 'b3c99e0c-f3a6-43a9-a945-1be838c2cc1b';

        //API Access domain
        $site_domain = $site_subdomain.'.api.oneall.com';

        //Connection Resource
        //http://docs.oneall.com/api/resources/connections/read-connection-details/
        $resource_uri = 'https://'.$site_domain.'/connections/'.$token .'.json';
        if(!\function_exists("curl_init"))
            throw new \zinux\kernel\exceptions\invalidOperationException("Function `<a href='http://www.php.net/manual/en/book.curl.php' target='__blank'>\\curl</a>` not found! It seems you have to install it on your system first!");
        //Setup connection
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $resource_uri);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_USERPWD, $site_public_key . ":" . $site_private_key);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_FAILONERROR, 0);

        //Send request
        $result_json = curl_exec($curl);
 
        //Error
        if ($result_json === false)
        {
          //You may want to implement your custom error handling here
          echo 'Curl error: ' . curl_error($curl). '<br />';
          echo 'Curl info: ' . curl_getinfo($curl). '<br />';
          curl_close($curl);
          return;
        }
        //else: Success
        //Close connection
        curl_close($curl);

        //Decode
        $json = json_decode ($result_json);
        //Extract data
        $data = $json->response->result->data;

        //Check for service
        switch ($data->plugin->key)
        {
          //Social Login
          case 'social_login':

          //Single Sign On
          case 'single_sign_on':

            //Operation successfull
            if ($data->plugin->data->status == 'success')
            {
                //The user_token uniquely identifies the user 
                //that has connected with his social network account
                $user_token = $data->user->user_token;

                //The identity_token uniquely identifies the social network account 
                //that the user has used to connect with
                $identity_token = $data->user->identity->identity_token;
                if(!isset($data->user->identity->emails) || !\is_array($data->user->identity->emails) || !\count($data->user->identity->emails))
                    throw new \zinux\kernel\exceptions\invalidOperationException("No email address provided!!");
                $primary_email_address = \array_shift($data->user->identity->emails);
                if(!@$primary_email_address->value)
                    throw new \zinux\kernel\exceptions\invalidOperationException("No email address provided!!");                    
                // 1) Check if you have a userID for this token in your database
                $user_id = \core\db\models\user::Fetch($primary_email_address->value);
               # if($user_id) $user_id->delete();
                #$user_id = null;
                // 1a) If the userID is empty then this is the first time that this user 
                // has connected with a social network account on your website
                if ($user_id === null)
                {
                    
                    $this->request->params["username"] = \preg_replace("#([^a-z0-9])*#i", "", $primary_email_address->value);
                    $this->request->params["email"] = $primary_email_address->value;
                    $this->request->params["password"] = $identity_token.$user_token;
                    $this->request->params["conf-password"] = $identity_token.$user_token;
                    $this->signupAction(0);
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
                    $id = $data->user->identity;
                    $profile->first_name = $id->name->givenName;
                    $profile->last_name = $id->name->familyName;
                    if(@$id->gender)
                        $profile->is_male = \strtolower($id->gender) == "male" ? 1 : 0;
                    if(isset($id->thumbnailUrl) || isset($id->pictureUrl))
                    {
                        # setting the profile settings for avatar custom upload
                        $profile->setSetting("/profile/avatar/custom/set",1, 0);
                        if(isset($id->pictureUrl))
                            # setting the profile settings for original image path
                            $profile->setSetting("/profile/avatar/custom/origin_image", $id->pictureUrl, 0);
                        if(isset($id->thumbnailUrl))
                            # setting the profile settings for thumbnail image path
                            $profile->setSetting("/profile/avatar/custom/thumb_image", $id->thumbnailUrl, 0);
                        # activate the custrom profile
                        $profile->setSetting("/profile/avatar/activated", "custom");
                    }
                    foreach($id->emails as $email)
                    {
                        $profile->public_email = "{$profile->public_email};".\array_filter($email->value, "strlen");
                    }
                    $profile->save();
                    # flag the redirection to profile creation
                    $this->request->params["continue"] = "/profile/edit";
                    $this->signinAction();
                    die("OK");
                  // 1a1) Create a new user account and store it in your database
                  // Optionally display a form to collect  more data about the user.
                  $user_id = sha1($token);

                  // 1a2) Attach the user_token to the userID of the created account.
                  LinkUserTokenToUserId ($user_token, $user_id);
                }
                // 1b) If you DO have an userID for the user_token then this user has
                // already connected before
                else
                {
                    $user_id->Signin($user_id, 1);
                    
                    $this->Redirect();
                }

                // 2) You have either created a new user or read the details of an existing
                // user from your database. In both cases you should now have a $user_id 

                // 2a) Create a Single Sign On session
                // $sso_session_token = GenerateSSOSessionToken ($user_token, $identity_token); 
                // If you would like to use Single Sign on then you should now call our API
                // to generate a new SSO Session: http://docs.oneall.com/api/resources/sso/

                // 2b) Login this user
                // You now need to login this user, exactly like you would login a user
                // after a traditional (username/password) login (i.e. set cookies, setup 
                // the session) and forward him to another page (i.e. his account dashboard)    
            }
            else
            {
                throw new \zinux\kernel\exceptions\invalidOperationException("Operation was <b>not successfull</b>!");
            }
            break;
          default:
              throw new \zinux\kernel\exceptions\invalidOperationException("Un-expected operation work-flow!!");
              /**
               * Alert the ower by email!
               */
        }
    }

    /**
    * The \modules\authModule\controllers\indexController::oauth2callbackAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function oauth2callbackAction()
    {
        $gauth = new \modules\authModule\models\gAuth();
        if(!$gauth->getInfo())
            die("No data has been recieved");
        $gauth->authenticate($_GET["code"]);
        \zinux\kernel\utilities\debug::_var($gauth->getInfo(), 1);
    }
}