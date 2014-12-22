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
    const PROFILE_DONE = 3;
    const PROFILE_BACKWARD = 4;
    const PROFILE_FORWARD = 4;
    
    public function Initiate() {
        parent::Initiate();
        # default title
        $this->layout->AddTitle("Toratan");
        # browser never should cache the profile's page
        header("Last-Modified: ".gmdate('D, d M Y H:i:s ')."GMT");
    }
    /**
     * Fetches the profile based on current request and loads it onto current view's handler
     * @throws \zinux\kernel\exceptions\invalidOperationException if any invalid request
     * @throws \zinux\kernel\exceptions\notFoundException If profile not found
     */
    protected function fetchProfile() {
        # fetch possible profile ID
        $profile_id = $this->request->GetIndexedParam(0); 
        # if user not signed in?
        if(!\core\db\models\user::IsSignedin())
        {
            # and also misses the profile ID
            if(!$this->request->CountIndexedParam())
                # this would be an invalid operations
                throw new \zinux\kernel\exceptions\invalidOperationException("Empty profile ID!");
            # otherwise user the first argument as profile ID
            if(!($user = \core\db\models\user::find(array("conditions"=> array("user_id = ? OR username = ?", $profile_id, $profile_id)))))
                # if not found, indicate it
                throw new \zinux\kernel\exceptions\notFoundException("The profile not found.");
            goto __FETCH_PROFILE;
        }
        # default user for profile viewing, is current user
        $user = \core\db\models\user::GetInstance();
        # load intial profile
        # if any profile ID is demaned
        if($profile_id)
            # if the profile id has found, we cool to proceed
            if(!in_array(strtolower($profile_id), array("page", "timeline")) && !($user = \core\db\models\user::find(array("conditions"=> array("user_id = ? OR username = ?", $profile_id, $profile_id)))))
                # otherwise indicate profile not found
                throw new \zinux\kernel\exceptions\notFoundException("The profile not found.");
__FETCH_PROFILE:
        # update the title with user's name
        $this->layout->AddTitle($user->get_RealName_or_Username()." on Toratan");
        # fetch a profile by the provided user instance
        $this->view->profile = \core\db\models\profile::getInstance($user->user_id, 0, 0);
        # set the related user
        $this->view->user = $user;
        # flag that current user is owner of the profile or not?
        $this->view->is_owner = (@\core\db\models\user::GetInstance()->user_id == $this->view->profile->user_id); ;
    }
    /**
    * The modules\opsModule\controllers\profileController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        # fetch the profile
        $this->fetchProfile();
        # if the profile is belong to current user?
        if(@\core\db\models\user::GetInstance()->user_id == $this->view->profile->user_id)
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
        # change the layout
        $this->layout->SetLayout("profile");
        # because of calling from `self::aboutAction()` we need to set it to `indexView`
        $this->view->setView("index");
        # set current active type
        $this->view->active_type = "about";
    }

    /**
    * The \modules\opsModule\controllers\profileController::editAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function editAction()
    {
        # we need a wide layout
        $this->layout->SetLayout("wide");
        # load intial profile
        $this->view->profile = \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id, 0, 0);
        # set specific title
        $this->layout->AddTitle(\core\db\models\user::GetInstance()->get_RealName_or_Username(). " - Editing profile");
        # intial value assignments
        $current_step = $this->view->step = 1;
        # provide hash value for view
        $this->view->hash = \zinux\kernel\security\security::__get_uri_hash_string(array(session_id()));
        # init error container
        $this->view->errors = array();
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
                #throw new \zinux\kernel\exceptions\invalidOperationException("Miss-configured input!!!");
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
                    if($current_step == 1)
                        # make user back to step `1` again!
                        $this->view->step = 2;
                    # decreasing step count
                    $this->view->step--;
                    #   indicate that profile backwarded
                    $profile_status = self::PROFILE_BACKWARD;
                    # purging un-necessary indexes
                    unset($this->request->POST['back']);
                    break;
                # are we going to forward?
                case isset($this->request->params['next']):
                    # increasing step count
                    $this->view->step++;
                    #   indicate that profile forwarded
                    $profile_status = self::PROFILE_FORWARD;
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
                    # go to save ops section
                    goto __SAVE_OPS;
                case isset($this->request->params['cancel']):
                    #   indicate that profile is done
                    $profile_status = self::PROFILE_DONE;
                    # purging un-necessary indexes
                    unset($this->request->POST['cancel']);
                    # go to deploy section
                    goto __DEPLOY;
                default:
                    throw new \zinux\kernel\exceptions\invalidOperationException;
            }
__SAVE_OPS:
            if(!$this->validate_inputs($current_step)) {
                # back to current step(do not proceed)
                $this->view->step = $current_step;
                return;
            }
            # save on session cache socket
            $sc->save("step#$current_step", $this->request->POST);
__DEPLOY:
            # if we are on submit mession?
            switch(@$profile_status) {
                case self::PROFILE_FORWARD:
                case self::PROFILE_BACKWARD:
                    if($sc->isCached("step#{$this->view->step}"))
                        foreach($sc->fetch("step#{$this->view->step}") as $index => $value) {
                            if(is_array($value)) $value =implode(";", $value);
                            $this->view->profile->$index = $value;
                        }
                    break;
                case self::PROFILE_CREATED:
                case self::PROFILE_SKIPPED:
                    # submit profile with created session cache
                    $this->submitProfile($sc);
                    # save the profile creation status
                    \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id, 0, 0)->setSetting("/profile/status", $profile_status);
                case self::PROFILE_DONE:
                    # destroy any data on session cache
                    $sc->deleteAll();
                    # relocate the browser
                    if(\core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id, 0, 0)->getSetting("/profile/status"))
                        header("location: /profile");
                    else
                        header("location: /");
                    exit;                    
            }
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
     * Validates {$this->request->params}
     * @param ineteger $current_step
     * @return bool true if validation successed; otherwise false
     */
    private function validate_inputs($current_step) {
        # the validator metadata
        # note: occurrence_step can be array of multiple step# e.g ... "occurrence_step" => array(1, 3, 5)
        $validators = array(
           "first_name" => array(
                   "occurrence_step"   => 1,
                   "func"                         =>"strlen", 
                   "e-msg"                      => "First name cannot be empty"),     
           "last_name" => array(
                   "occurrence_step"     => 1,
                   "func"                           =>"strlen", 
                   "e-msg"                        => "Last name cannot be empty"),     
        );
        # the validator iterator
        foreach($validators as $index => $validator) {
            # check if validation function exists?
            if(!function_exists($validator["func"]))
                throw new \zinux\kernel\exceptions\invalidOperationException("Function `{$validator["func"]}` not found.");
            # make indexes can be occure in multiple steps
            if(!is_array($validator["occurrence_step"]))
                $validator["occurrence_step"] = array($validator["occurrence_step"]);
            # current validator should be launched in current step#
            if(in_array($current_step, $validator["occurrence_step"])) {
                # fetch param's value
                $value = @$this->request->params[$index];
                # if validation failed?
                if(!isset($value) || !@$validator["func"]($value))
                    # inject the error message
                    $this->view->errors[$index] = isset($validator["e-msg"]) ? $validator["e-msg"] : "Invalid parameter";
            } else {
                # if we meet an index which is not expected!!??
                if(isset($this->request->params[$index]))
                    # output an error too!
                    $this->view->errors[$index] = "Un-expected `$index` parameter";
            }
        }
        # if any error exists? validation fails; otherwise the params are valid
        return count($this->view->errors) > 0 ? false : true;
    }
    /**
     * Submits a profile data saved on a session cache
     * @param \zinux\kernel\caching\sessionCache $session_cache profile data container
     * @throws \zinux\kernel\exceptions\invalidArgumentException if $session_cache is NULL
     */
    private function submitProfile(\zinux\kernel\caching\sessionCache $session_cache)
    {
        # check session cache instance existance
        if(!$session_cache)
            throw new \zinux\kernel\exceptions\invalidArgumentException("NULL cache passed!!");
        # fetch the user's profile instance
        $profile = \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id, 0, 0);
        # foreach value stored in cache
        foreach($session_cache->fetchAll() as $value)
        {
            # the head values should always be an array
            if(!\is_array($value))
                throw new \zinux\kernel\exceptions\invalidArgumentException("Invalid input! profile was not created....");
            # since the $value is an array already
            # make an iteration on its subvalue
            foreach($value as $key=> $_value)
            {
                # if its subvalue is a pure value(i.e not mixed)
                if(!\is_array($_value))
                    # just add the value directly into profile
                    $profile->$key = htmlspecialchars($_value);
                # else if it is an array
                else
                    # make a string from subvalue's implosion
                    $profile->$key = \implode(";", \array_filter($_value, "strlen"));
            }
        }
        # set random cover image from cover-sample
        $this->randomCoverAction($profile);
        # save the profiles
        $profile->save();
    }
    /**
     * Sets random cover from cover's samples; Note that it only will set the profile's setting, it will not save them,
     * you have to save the profilefrom outside of method.
     * @access /profile/randomcover
     * @hash-sum array($profile->user_id, session_id())
     * @param \core\db\models\profile $profile The target profile to change its cover
     * @return boolean Returns TRUE on successfull setting; otheriwse FALSE
     */
    public function randomCoverAction(\core\db\models\profile &$profile = NULL) {
        # flag that if this method is called from inside of current class?
        $inline_invoked = $profile ? TRUE : FALSE;
        # if the user just editing its profile info and proviously a profile cover image selected?
        if($inline_invoked && $profile->getSetting("/profile/cover/image"))
            # do not proceed
            return;
        # if this is outside request?
        if(!$profile) {
            # assume current profile as passed profile
            $profile =\core\db\models\profile::getInstance(NULL, 0, 0);
            # since this is an outside request, we need to validate it
            \zinux\kernel\security\security::__validate_request($this->request->params, array($profile->user_id, session_id()));
        }
        # malloc an error-tag container
        $error_tag = -1;
        # fetch the samples's path from config file
        $samples_path = "/".\zinux\kernel\application\config::GetConfig("upload.cover.sample_path");
        # if no config set?
        if(!$samples_path) { $error_tag = -1; goto __ERROR; }
        # fetch the info file on samples
        $sample_info = PUBLIC_HTML."$samples_path/info.json";
        # validate the file
        if(!file_exists($sample_info) || !is_readable($sample_info)) { $error_tag = 0; goto __ERROR; }
        # this file in JSON format, decode it
        $info = json_decode(file_get_contents($sample_info));
        # if there is problem with JSON decode?
        if(!$info) { $error_tag = 1; goto __ERROR; }
        # seed the rand
        srand(str_replace(array(" ", "0."), array("", ""), microtime()));
        # Don't knwow why? but sometimes $cover won't set right!!
        for($i = 0; $i<10; $i++) {
            # pick a random cover from info
            $cover = @$info[rand(0, count($info))];
            # if setted? we are ok break the loop
            if($cover) break;
            # sleep for 1000ns
            time_nanosleep(0, 1000);
            # continue with loop
        }
        # if the re-tries overflow?
        if($i >= 10) { $error_tag = 2; goto __ERROR; }
        # set randomized cover
        $profile->setSetting("/profile/cover/image", "{$samples_path}{$cover->file_name}", 0);
        $profile->setSetting("/profile/cover/info", "&COPY; <a href='{$cover->origin_link}' target='__blank'>`{$cover->title}` @{$cover->by}</a> <span class='glyphicon glyphicon-share-alt small'>", 0);
        # if we reach here it mean we have succeed to set cover
        # if this is an internal call
        if($inline_invoked)
            # indicate the success
            return true;
        # otherwise save the profile
        $profile->save();
        # redirect to the profile
        header("location: /profile");
        # exit
        exit;
        # if there is any error? this section will handle
__ERROR:
        # if this is an internal call
        if($inline_invoked)
            # indicate the failure
            return false;
        # otherwise just throw an exception
        throw new \RuntimeException("While randomizing the cover photo, an error raised with error-tag `$error_tag`.");
    }
    /**
     * Removes cover; Note that it only will set the profile's setting, it will not save them, you have to save the profilefrom outside of method.
     * @access /profile/removecover
     * @hash-sum array($profile->user_id, session_id())
     * @param \core\db\models\profile $profile The target profile to change its cover
     * @return boolean Returns TRUE on successfull setting; otheriwse FALSE
     */
    public function removeCoverAction(\core\db\models\profile &$profile = NULL) {
        # flag that if this method is called from inside of current class?
        $inline_invoked = $profile ? TRUE : FALSE;
        # if this is outside request?
        if(!$profile) {
            # assume current profile as passed profile
            $profile =\core\db\models\profile::getInstance(NULL, 0, 0);
            # since this is an outside request, we need to validate it
            \zinux\kernel\security\security::__validate_request($this->request->params, array($profile->user_id, session_id()));
        }
        # fetch cover details
        $image = $profile->getSetting("/profile/cover/image");
        $info = $profile->getSetting("/profile/cover/info");
        # if this is a custom cover?
        if(!$info)
            # unlink the profile cover
            @\shell_exec("rm -f .$image");
        # unset the settings
        $profile->unsetSetting("/profile/cover/image", 0);
        $profile->unsetSetting("/profile/cover/info", 0);
        # if we reach here it mean we have succeed to remove cover
        # if this is an internal call
        if($inline_invoked)
            # indicate the success
            return true;
        # otherwise save the profile
        $profile->save();
        # redirect to the profile
        header("location: /profile");
        # exit
        exit;
        
    }
    /**
    * The \modules\opsModule\controllers\profileController::avatarAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function avatarAction()
    {
        # we only response to POST requests
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\accessDeniedException;
        # valdiate hashsum
        \zinux\kernel\security\security::__validate_request($this->request->params, array(\core\db\models\user::GetInstance()->user_id, session_id()));
        # validate inputs
        \zinux\kernel\security\security::IsSecure($_FILES, array("upload-avatar"));
        # check if there is any upload error?
        if($_FILES["upload-avatar"]["error"] != UPLOAD_ERR_OK)
            throw new \core\exceptions\uploadException($_FILES["upload-avatar"]["error"]);
        # fetch the profile
        $profile = \core\db\models\profile::getInstance(NULL, 0, 0);
        # invoke avatar's upload ops
        $this->upload_avatar("upload-avatar", $profile);
        # save the profile
        $profile->save();
        # crop the image
        header("location: /profile/avatar/crop");
        die;
    }
    /**
     * A safe avatar uploader
     * @param array $_FILES the files inputs
     * @param \core\db\models\profile $profile A profile instance
     * @throws \zinux\kernel\exceptions\invalidArgumentException if no data found in $_FILES
     * @return boolean TRUE if upload was successful; otherwise FALSE
     */
    protected function upload_avatar($index_name, \core\db\models\profile &$profile)
    {
        # validate the $_FILES input
        if(!count($_FILES))
            throw new \zinux\kernel\exceptions\invalidArgumentException("No file uploaded!");
        # define supported format
        $image_support_types = array('png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg');
        # check format
        if(!in_array($_FILES[$index_name]["type"], $image_support_types))
                throw new \zinux\kernel\exceptions\appException("Only the following file types are supported `".implode(", ", array_keys($image_support_types))."`.");
        # get complete path of origin image and its thumbnail path
        list($orig_path, $thum_path) = \modules\opsModule\models\avatarPathName::generate($_FILES[$index_name]["tmp_name"], $profile);
        # move uplaoded file to its proper location and name
        if(!\move_uploaded_file($_FILES[$index_name]["tmp_name"], $orig_path))
            throw new \core\exceptions\uploadException(UPLOAD_ERR_CANT_WRITE);
        # set origin path in profile
        \modules\opsModule\models\avatarPathName::setProfile($profile, "/$orig_path");
        # create a thumbnail for original image
        if(!@\core\ui\html\avatar::make_thumbnail($orig_path, $thum_path))
            throw new \zinux\kernel\exceptions\invalidOperationException("File uploaded but unable to create thumbnail!");
        # set thumbnail path in profile
        \modules\opsModule\models\avatarPathName::setProfile($profile, NULL, "/$thum_path");
    }
    

    /**
    * The \modules\opsModule\controllers\profileController::avatar_cropAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function avatar_cropAction()
    {
        # invoke a new instance of profile
        $profile = \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id, 0, 0);
        # pass any info we have on the user's profile's avatar to view
        $this->view->avatar = $profile->getSetting("/profile/avatar/");
        # if user does not have any custom avatar
        if(!isset($this->view->avatar->image) || !\file_exists(".".$this->view->avatar->image))
            throw new \zinux\kernel\exceptions\notFoundException("No avatar found");
        # we don't process GET request here
        if($this->request->IsGET()) return;
        # make sure that inputs are secure
        \zinux\kernel\security\security::IsSecure($this->request->params, array('x', 'y', 'w', 'h'));
        # validate the inputs
        foreach (array('x','y','w','h') as $value)
            if(!\is_numeric($_POST[$value]))
                throw new \zinux\kernel\exceptions\invalidArgumentException;
        # make a crop based on inputs
        \core\ui\html\avatar::make_crop(
                ".".$this->view->avatar->image,
                ".".$this->view->avatar->thumbnail,
                $this->request->params['x'],
                $this->request->params['y'],
                $this->request->params['w'],
                $this->request->params['h']);
        # relocate the browser
        header("location: /profile");
        exit;
    }

    /**
    * Provides view access to profile
    * @access via /ops/avatar/view/(PROFILE_ID)?hash_sum
    * @hash-sum PROFILE_ID
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function avatar_viewAction()
    {
        if($this->request->CountIndexedParam() < 1)
            throw new \zinux\kernel\exceptions\invalidOperationException;
        \zinux\kernel\security\security::__validate_request($this->request->params, array($this->request->GetIndexedParam(0)));
        $this->view->profile = \core\db\models\profile::getInstance($this->request->GetIndexedParam(0), 0, 0);
        if(!$this->view->profile)
            throw new \zinux\kernel\exceptions\notFoundException("Profile not found!");
    }

    /**
    * The \modules\opsModule\controllers\profileController::aboutAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function aboutAction() { $this->IndexAction(); }

    /**
    * The \modules\opsModule\controllers\profileController::postsAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function postsAction()
    {
        # fetch the profile
        $this->fetchProfile();
        # if no page param. passed assume it is page 1
        if(!isset($this->request->params["page"]) || $this->request->params["page"] < 1)
            $this->request->params["page"] = 1;
        if(isset($this->request->params['infscroll']))
            $this->layout->SuppressLayout();
        # define limit
        $limit = 10;
        # define starting offset
        $offset = ($this->request->params["page"] - 1) * $limit;
        $n = new \core\db\models\note;
        # malloc condition
        $cond = array();
        if(isset($this->request->params["timeline"]))
            $cond = array("EXTRACT(YEAR FROM updated_at) <= ?", $this->request->params["timeline"]);
        # fetch public notes
        $this->view->posts = 
                $n->fetchItems($this->view->profile->user_id, \core\db\models\item::WHATEVER,
                    # public
                    \core\db\models\note::FLAG_SET, 
                    # not trash
                    \core\db\models\note::FLAG_UNSET,
                    # don't care if archived
                    \core\db\models\note::WHATEVER,
                    # only select the below columns.
                    array("select" => "note_id, note_title, note_summary, updated_at", "offset" => $offset, "limit" => $limit, "order" => "updated_at desc", "conditions" => $cond));
        # if we are not in infinit-scrolling mode and there are at least a post
        if(!isset($this->request->params['infscroll'])) {
            # get the year of NOW
            $cd = Date("Y");
            # get the year of account creation date
            $ed = Date("Y", strtotime($this->view->profile->created_at));
            # malloc an array
            $this->view->timeline = array();
            # collect the differences
            for(; $ed <= $cd; $cd--)
                $this->view->timeline[] = $cd;
            # init the timeline param
            if(!isset($this->request->params["timeline"]))
                $this->request->params["timeline"] = $this->view->timeline[0];
        }
        # fetch total public notes
        $this->view->total_count = 
                $n->count(
                    array(
                            "conditions" => array(
                                # cond. on owner_id | is_public | is_trash and previously generated cond.
                                "owner_id = ? AND is_public = ? AND is_trash = ? AND ".(count($cond) ? "{$cond[0]}" : "?"),
                                # pass the profile's user ID
                                $this->view->profile->user_id,
                                # we onlyt want public items
                                \core\db\models\note::FLAG_SET,
                                # don't count those which are trash
                                \core\db\models\note::FLAG_UNSET,
                                # merging condition values
                                (count($cond) ? $cond[1] : 1)
                        )
                    )
                );
        # pass the fetch limit value
        $this->view->fetch_limit = $limit;
        # calc if there is more note?
        $this->view->is_more_note = (ceil($this->request->params["page"] + 1) <= ceil($this->view->total_count / $limit));
        # change the layout
        $this->layout->SetLayout("profile");
        # fail-safe for pre-view mode
        $this->view->setView("posts");
        # set current active type
        $this->view->active_type = "posts";
    }
    /**
    * The \modules\opsModule\controllers\profileController::previewAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function previewAction()
    {
        $action = "index";
        if($this->request->CountIndexedParam() !== 0) {
            $action = $this->request->GetIndexedParam(0);
        }
        switch(strtolower($action)) {
            case "index":
            case "posts":
            case "about":
                break;
            default: $action = "index";
        }
        if(!method_exists($this, "{$action}Action"))
            throw new \zinux\kernel\exceptions\invalidOperationException("Undefined method `".__CLASS__."::{$action}Action()`");
        # remove the action name from params
        array_shift($this->request->indexed_param);
        # re-init the request params
        $this->request->params = array();
        # re-gen. `params` array from `indexed_param`
        while(count($this->request->indexed_param)) {
            $key = array_shift($this->request->indexed_param);
            $value = @array_shift($this->request->indexed_param);
            $this->request->params[$key] = $value; 
        }
        # re-gen. `indexed_param`
        $this->request->GenerateIndexedParams();
        # call the target action
        $this->{"{$action}Action"}();
        # make it a public view
        $this->view->is_owner = 0;
        # flag preview mode
        $this->view->preview_mode = 1;
    }
    /**
    * The \modules\opsModule\controllers\profileController::coverAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function coverAction()
    {
        $this->view->samples_path =\zinux\kernel\application\config::GetConfig("upload.cover.sample_path");
        if(!$this->view->samples_path)
            throw new \zinux\kernel\exceptions\notFoundException("No configuartion found for `upload.cover.sample_path`.");
        if($this->request->IsGET()) {
            $this->layout->AddTitle("Change Cover |  Toratan");
            $this->layout->SetLayout("wide");
            return;
        }
        if(!$this->request->IsPOST())
            throw new \zinux\kernel\exceptions\accessDeniedException;
        # validate the request
        \zinux\kernel\security\security::__validate_request($this->request->params, array(\core\db\models\user::GetInstance()->user_id, session_id()));
        # validate if we are uploading a cover image?
        if(!array_key_exists("upload-cover", $_FILES)) {
            # otherwise are we using sample cover images?
            # we only accept ajax requests for sampling!!
            \zinux\kernel\security\security::IsSecure($this->request->params, array("sample", "id", "info", "ajax"));
            # if we reach here it means we are going to use sample cover images
            $profile = \core\db\models\profile::getInstance(NULL, 0, 0);
            # unlink any possible perviously profile picture
            @\shell_exec("rm -f .".$profile->getSetting("/profile/cover/image"));
            # set the cover image path, in this part treat the sample cover as a normal cover photo
            $profile->setSetting("/profile/cover/image", "/{$this->view->samples_path}".$this->request->params["id"], 0);
            # we save the sample-cover information as well
            $profile->setSetting("/profile/cover/info", $this->request->params["info"]);
            # response with json format
            die('{"success":1,"redirect":1,"location":"/profile"}');
        }
        # if we reach here it mean we are uploading image!!
        $F = $_FILES["upload-cover"];
        if($F["error"] != UPLOAD_ERR_OK)
            throw new \core\exceptions\uploadException($F["error"]);
        # fetch upload location for uploaded image 
        $upload_path = \zinux\kernel\application\config::GetConfig("upload.cover.image_path");
        # if we have a miss configured project
        if(!$upload_path)
            # indecate it
            throw new \zinux\kernel\exceptions\notImplementedException("No configuration found for `upload.cover.image_path`!!");
        # define supported format
        $image_support_types = array('png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg');
        # check format
        if(!in_array($F["type"], $image_support_types))
                throw new \zinux\kernel\exceptions\appException("Only the following file types are supported `".implode(", ", array_keys($image_support_types))."`.");
        # fetch the file's name
        $fname = $F["name"];
        # fetch file's extention
        $ext = end(\array_filter(explode(".", $fname)));
        # fetch user's profile
        $profile = \core\db\models\profile::getInstance(NULL, 0, 0);
        # unlink any possible perviously profile picture
        @\shell_exec("rm -f .".$profile->getSetting("/profile/cover/image"));
        # define a counter for naming
        $counter = 0;
        # while original image file already exists, increase the counters
        while(\file_exists($upload_path.sha1($fname.(++$counter)).".$ext")) ;
        # generate a new name for original image
        $fname = sha1($fname.$counter);
        # generate the original image's paths
        $upload_path .= "$fname.$ext";
        # calculate what function we will use to create image from?
        # note that the `$F[type]` already secured with `$image_support_types`
        # so it won't calling a method that does not exist.
        $image_func = "imagecreatefrom".  str_replace("image/", "", $F["type"]);
        # reate a new image from uploaded file
        $src = $image_func($F['tmp_name']);        
        # get uploaded image's size
        list($width, $height) = getimagesize($F['tmp_name']); 
        # if the image's width is not standrad
        # for band-width reasons we will always resize images { width: 1200px }
        if(true || $width < 1200) {
            # we go with 1200px width
            $newwidth = 1200;
            # re-calc new scaled width
            $newheight = ($height / $width) * $newwidth; 
            # create a new true color image
            $target_img = imagecreatetruecolor($newwidth, $newheight);
            # copy and resize uploaded image with resampling
            imagecopyresampled($target_img, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        } else 
            # if the uploaded image has a standard width?
            # just consider src image as target image
            $target_img = $src;
        # output image to file
        if(!@imagejpeg($target_img, $upload_path, 100))
            throw new \core\exceptions\uploadException(UPLOAD_ERR_CANT_WRITE);
        # release used resources 
        imagedestroy($src);
        imagedestroy($target_img);
        # setting the profile settings for original image path
        $profile->setSetting("/profile/cover/image", "/$upload_path", 1);
        # dispose any previously saved info in sample cover
        $profile->unsetSetting("/profile/cover/info");
        # redirect to user's profile
        header("location: /profile");    
    }
    /**
    * The \modules\opsModule\controllers\profileController::setAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function setAction()
    {
        \zinux\kernel\security\security::IsSecure($this->request->params, array("keys", "values"), array("keys" => "is_array", "values" => "is_array"));
        \zinux\kernel\security\security::__validate_request($this->request->params);
        if(count($this->request->params["keys"]) !== count($this->request->params["values"]))
            throw new \zinux\kernel\exceptions\invalidArgumentException("The no match between count of `keys` and `values`!");
        $keys = $this->request->params["keys"];
        $values = $this->request->params["values"];
        $profile =\core\db\models\profile::getInstance();
        for($index=0; $index < count($keys); $index++) {
            $profile->setSetting("{$keys[$index]}", $values[$index], 0);
        };
        $profile->save();
        exit;
    }
}
