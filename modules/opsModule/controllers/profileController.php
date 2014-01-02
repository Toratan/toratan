<?php
namespace modules\opsModule\controllers;
    
/**
 * The modules\opsModule\controllers\profileController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class profileController extends \zinux\kernel\controller\baseController
{
    const PROFILE_NOT_CREATED = 0;
    const PROFILE_SKIPPED = 1;
    const PROFILE_CREATED = 2;
    /**
    * The modules\opsModule\controllers\profileController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        $this->layout->AddTitle("Profile viewing....");
        # if user not signed in?
        if(!\core\db\models\user::IsSignedin())
        {
            # and also misses the profile ID
            if(!$this->request->CountIndexedParam())
                # this would be an invalid operations
                throw new \zinux\kernel\exceptions\invalideOperationException("Empty profile ID!");
            # otherwise user the first argument as profile ID
            if(!($user = \core\db\models\user::find(array("conditions"=> array("user_id = ?", $this->request->GetIndexedParam(0))))))
                # if not found, indicate it
                throw new \zinux\kernel\exceptions\notFoundException("The profile not found.");
            # pass the profile instance to view
            $this->view->profile = \core\db\models\profile::getInstance($user->user_id);
            # do not proceed the following code-lines
            return;
        }
        # default user for profile viewing, is current user
        $user = \core\db\models\user::GetInstance();
        # load intial profile
        # if any profile ID is demaned
        if($this->request->CountIndexedParam())
            # if the profile id has found, we cool to proceed
            if(!($user = \core\db\models\user::find(array("conditions"=> array("user_id = ?", $this->request->GetIndexedParam(0))))))
                # otherwise indicate profile not found
                throw new \zinux\kernel\exceptions\notFoundException("The profile not found.");
        # fetch a profile by the provided user instance
        $this->view->profile = \core\db\models\profile::getInstance($user->user_id);
        # if the profile is belong to current user?
        if(\core\db\models\user::GetInstance()->user_id == $this->view->profile->user_id)
        {
            # if so, fetch profile status
            $profile_status = $this->view->profile->getSetting("/profile/status") ;
            # if profile not already created
            if(!$profile_status)
            {
                # invoke edit action
                $this->editAction();
                # set view to edit mode
                $this->view->setView("edit");
                # return from index action
                return;
            }
        }
    }

    /**
    * The \modules\opsModule\controllers\profileController::editAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function editAction()
    {
        $this->layout->AddTitle("Profile editing....");
        # we need a wide layout
        $this->layout->SetLayout("wide");
        # load intial profile
        $this->view->profile = \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id);
        # intial value assignments
        $current_step = $this->view->step = 1;
        # provide hash value for view
        $this->view->hash = \zinux\kernel\security\security::GetHashString(array(session_id()));
        /** open up a session cache socket **/
        $sc = new \zinux\kernel\caching\sessionCache(__METHOD__);
        try
        {
            # check if step index is in effect
            if(isset($this->request->params['step']))
            {
                # update the intial values
                $current_step = $this->view->step = $this->request->params['step'];
                # purging un-necessary indexes
                unset($this->request->POST['step']);
            }
            # otherwise
            else $this->view->step=1;
                # this is an error
                #throw new \zinux\kernel\exceptions\invalideOperationException("Miss-configured input!!!");
            # if not a POST req.
            if(!$this->request->IsPOST())
                # just show the view
                return;
            /**
             * fetch step action
             */
            switch(true)
            {
                # are we going to back?
                case isset($this->request->params['back']):
                    if($current_step==1)
                        throw new \zinux\kernel\exceptions\invalideOperationException("Cannot go back!!!");
                    # decreasing step count
                    $this->view->step--;
                    # purging un-necessary indexes
                    unset($this->request->POST['back']);
                    break;
                # are we going to forward?
                case isset($this->request->params['next']):
                    # increasing step count
                    $this->view->step++;
                    # purging un-necessary indexes
                    unset($this->request->POST['next']);
                    break;
                # are we finishing?
                case isset($this->request->params['finish']):
                    #   indicate that profile created
                    $profile_status = self::PROFILE_CREATED;
                    # purging un-necessary indexes
                    unset($this->request->POST['finish']);
                    # go to save ops section
                    goto __SAVE_OPS;
                # are we going to skip?
                case isset($this->request->params['skip']):
                    #   indicate that profile skipped
                    $profile_status = self::PROFILE_SKIPPED;
                    # purging un-necessary indexes
                    unset($this->request->POST['skip']);
                    # save ops section
    __SAVE_OPS:
                    # save on session cache socket
                    $sc->save("step#$current_step", $this->request->POST);
                    # submit profile with created session cache
                    $this->submitProfile($sc);
                    # save the profile creation status
                    \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id)->setSetting("/profile/status", $profile_status);
                case isset($this->request->params['cancel']):
                    # destroy any data on session cache
                    $sc->deleteAll();
                    # relocate the browser
                    if(\core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id)->getSetting("/profile/status"))
                        header("location: /profile");
                    else
                        header("location: /");
                    exit;
                
                default:
                    throw new \zinux\kernel\exceptions\invalideOperationException;
            }
            # save on session cache socket
            $sc->save("step#$current_step", $this->request->POST);
        }
        # IF ANYTHING HAPPENED
        catch(\Exception $e)
        {
            # DELETE ANY CACHED DATA
            $sc->deleteAll();
            # THROW THE EXCEPTION TO GENERAL EXCEPTION HANDLER
            throw $e;
        }
    }
    /**
     * Submits a profile data saved on a session cache
     * @param \zinux\kernel\caching\sessionCache $session_cache profile data container
     * @throws \zinux\kernel\exceptions\invalideArgumentException if $session_cache is NULL
     */
    private function submitProfile(\zinux\kernel\caching\sessionCache $session_cache)
    {
        # check session cache instance existance
        if(!$session_cache)
            throw new \zinux\kernel\exceptions\invalideArgumentException("NULL cache passed!!");
        # fetch the user's profile instance
        $profile = \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id);
        # foreach value stored in cache
        foreach($session_cache->fetchAll() as $value)
        {
            # the head values should always be an array
            if(!\is_array($value))
                throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid input! profile was not created....");
            # since the $value is an array already
            # make an iteration on its subvalue
            foreach($value as $key=> $_value)
            {
                # if its subvalue is a pure value(i.e not mixed)
                if(!\is_array($_value))
                    # just add the value directly into profile
                    $profile->$key = $_value;
                # else if it is an array
                else
                    # make a string from subvalue's implosion
                    $profile->$key = \implode(";", \array_filter($_value, "strlen"));
            }
        }
        # save the profiles
        $profile->save();
    }

    /**
    * The \modules\opsModule\controllers\profileController::avatarAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function avatarAction()
    {
        # invoke a new instance of profile
        $profile = \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id);
        # pass any info we have on the user's profile's avatar to view
        $this->view->avatar = $profile->getSetting("/profile/avatar/");
        # if we have a GET request
        # check if we have a custom avatar delete request?
        if($this->request->IsGET() && isset($this->request->params["delete"]))
        {
            # check if we have a custom upload in user's profile
            if(isset($this->view->avatar->custom))
            {
                # unlink the original image from hard drive
                \shell_exec("rm .".$this->view->avatar->custom->origin_image);
                # unlink the thumbnail image from hard drive
                \shell_exec("rm .".$this->view->avatar->custom->thumb_image);
                # unset the custom setting from database
                $profile->unsetSetting("/profile/avatar/custom");
            }
            # relocate the browser
            header("location: /profile/avatar");
            exit;
        }
        # we only process POST request here
        if(!$this->request->IsPOST()) return;
        # validate the inputs
        \zinux\kernel\security\security::ArrayHashCheck($this->request->params, array(\core\db\models\user::GetInstance()->user_id, \session_id()));
        # we do not support multiple uploads on avatars
        if(is_array($_FILES["custom"]["name"]))
            throw new \core\exceptions\uploadException(UPLOAD_ERR_CANT_WRITE);
        # if we have an uploaded file
        if(isset($_FILES["custom"]) && strlen($_FILES["custom"]['tmp_name']))
            # flag the upload
            $this->request->params["custom"] = "custom";
        # check for any possible errors
         if ($_FILES["custom"]["error"] && $_FILES["custom"]["error"] != UPLOAD_ERR_NO_FILE)
             throw new \core\exceptions\uploadException($_FILES["custom"]["error"]);
        # an error flag used during ops4
        $this->errors = array();
        # if we have no active field
        if(!isset($this->request->params["activated"]))
            # indicate the error
            return $this->view->errors[] = "No field activated!";
        # iterate on params
        foreach ($this->request->params as $key => $value)
        {
            # check for supported params
            switch($key)
            {
                case \core\ui\html\avatar::INSTAGRAM:
                case \core\ui\html\avatar::FACEBOOK:
                case \core\ui\html\avatar::GRAVATAR:
                case \core\ui\html\avatar::TWITTER:
                        # if we have no input in a input, it may means that user deleted that input
                        if(!strlen($value)) { $profile->unsetSetting ("/profile/avatar/$key"); break; }
                        # set the settings
                        $profile->setSetting("/profile/avatar/$key/set", 1, 0);
                        $profile->setSetting("/profile/avatar/$key/id", $value, 0);
                        break;
                case "custom":
                    # if we are going for custom upload section
                    # check for upload locations
                    foreach (array(PUBLIC_HTML."/access/img/upload", PUBLIC_HTML."/access/img/upload/thumbnail") as $value)
                        if(!file_exists($value))
                            if(!@mkdir ($value, 0777, 1))
                                    throw new \zinux\kernel\exceptions\invalideOperationException("Unable to create directroy `$value`.");
                    # upload the avatar
                    $this->upload_avatar($key, $profile);
                    break;
                case "activated":
                    # configure the activated section
                    if($value == "custom")
                        $profile->setSetting("/profile/avatar/activated", $value);
                    elseif(strlen($this->request->params[$value]))
                        $profile->setSetting("/profile/avatar/activated", $value, 0);
                    else
                        $this->errors[$value] = "Selected active field is empty";
                    break;
                default:
                    # unset any un-ettended params
                    unset($this->request->params[$key]);
                    break;
            }
        }
        # if we have errors?
        if(count($this->errors))
        {
            # notify the errors
            $this->view->errors = $this->errors;
            return;
        }
        # save the profile
        $profile->save();
        # relocate the browser
        # if any image has been uploaded
        if(isset($this->request->params["custom"]))
            # crop the image
            header("location: /profile/avatar/crop");
        else
            # otherwise relocate to /profile
            header("location: /profile");
        exit;
    }
    /**
     * A safe avatar uploader
     * @param array $_FILES the files inputs
     * @param \core\db\models\profile $profile A profile instance
     * @throws \zinux\kernel\exceptions\invalideArgumentException if no data found in $_FILES
     * @return boolean TRUE if upload was successful; otherwise FALSE
     */
    protected function upload_avatar($index_name, \core\db\models\profile &$profile)
    {
        # validate the $_FILES input
        if(!count($_FILES))
            throw new \zinux\kernel\exceptions\invalideArgumentException("No file uploaded!");
        # define supported format
        $image_support_types = array('png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif');
        # check format
        if(!in_array($_FILES[$index_name]["type"], $image_support_types))
        {
            $this->errors["custom"] = "File type not supported!";
            return;
        }
        # fetch upload location for original image 
        $orig_path = \zinux\kernel\application\config::GetConfig("upload", "avatar", "original_image_path");
        # fetch upload location for original image 
        $thum_path = \zinux\kernel\application\config::GetConfig("upload", "avatar", "thumbnail_image_path");
        # if we have a miss configured project
        if(!$orig_path || !$thum_path)
            # indecate it
            throw new \zinux\kernel\exceptions\invalideArgumentException("No configuration found for `upload.avatar`!!");
        # fetch file's extention
        $ext = end(\array_filter(explode(".", $_FILES[$index_name]["name"])));
        # fetch file's original name
        $alt_name = $_FILES[$index_name]["name"];
        # define a counter for naming
        $counter = 0;
        # unlink any possible perviously profile picture
        @\shell_exec("rm -f .".$profile->getSetting("/profile/avatar/custom/origin_image"));
        # while original image file already exists, increase the counters
        while(\file_exists($orig_path.sha1($alt_name.(++$counter)).".$ext")) ;
        # generate a new name for original image
        $alt_name = sha1($alt_name.$counter);
        # generate the original image's paths
        $orig_path .= "$alt_name.$ext";
        # define a counter for naming
        $counter = 0;
        # unlink any possible perviously profile picture
        @\shell_exec("rm -f .".$profile->getSetting("/profile/avatar/custom/thumb_image"));
        # while thumbnail image file already exists, increase the counters
        while(\file_exists($thum_path.sha1($alt_name.(++$counter)."-tmb").".$ext")) ;
        # generate the new name for thumbnail
        $thum_path .= sha1($alt_name.$counter."-tmb").".$ext";
        # move uplaoded file to its proper location and name
        if(!@\move_uploaded_file($_FILES[$index_name]["tmp_name"], $orig_path))
            throw new \core\exceptions\uploadException(UPLOAD_ERR_CANT_WRITE);
        # setting the profile settings for avatar custom upload
        $profile->setSetting("/profile/avatar/custom/set",1, 0);
        # setting the profile settings for original image path
        $profile->setSetting("/profile/avatar/custom/origin_image", "/$orig_path", 0);
        # create a thumbnail for original image
        if(!@\core\ui\html\avatar::make_thumbnail($orig_path, $thum_path))
            throw new \zinux\kernel\exceptions\invalideOperationException("File uploaded but unable to create thumbnail!");
        # setting the profile settings for thumbnail image path
        $profile->setSetting("/profile/avatar/custom/thumb_image", "/$thum_path", 0);
    }
    

    /**
    * The \modules\opsModule\controllers\profileController::avatar_cropAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function avatar_cropAction()
    {
        # invoke a new instance of profile
        $profile = \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id);
        # pass any info we have on the user's profile's avatar to view
        $this->view->avatar = $profile->getSetting("/profile/avatar/");
        # if user does not have any custom avatar
        if(!isset($this->view->avatar->custom) || !\file_exists(".".$this->view->avatar->custom->origin_image))
        {
            # unset any possible mis-configured setting
            $profile->unsetSetting("/profile/avatar/custom");
            # flag the error used in {self::avatarAction()}
            $this->view->errors["custom"] = "No avatar exists!";
            # promt the {self::avatarAction()}'s view
            $this->view->setView("avatar");
            # do not proceed
            return;
        }
        # shift the avatar link to its custom property
        $this->view->avatar = $this->view->avatar->custom;
        # we don't process GET request here
        if($this->request->IsGET()) return;
        # make sure that inputs are secure
        \zinux\kernel\security\security::IsSecure($this->request->params, array('x', 'y', 'w', 'h'));
        # validate the inputs
        foreach (array('x','y','w','h') as $value)
        {
            if(!\is_numeric($_POST[$value])) 
            {
                $pipe = new \core\utiles\messagePipe;
                $pipe->write("No <b>crop</b> processed!!!");
                goto __RELOCATE;
            }
        }
        # make a crop based on inputs
        \core\ui\html\avatar::make_crop(".".$this->view->avatar->origin_image, ".".$this->view->avatar->thumb_image, $this->request->params['x'],$this->request->params['y'], $this->request->params['w'], $this->request->params['h']);
__RELOCATE:
        # relocate the browser
        header("location: /profile/avatar");
        exit;
    }
}
