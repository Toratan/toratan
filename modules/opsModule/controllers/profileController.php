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
        # load intial profile
        $this->view->profile = \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id);
        # fetch profile status
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

    /**
    * The \modules\opsModule\controllers\profileController::editAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function editAction()
    {
        # load intial profile
        $this->view->profile = \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id);
        # intial value assignments
        $current_step = $this->view->step = 1;
        # provide hash value for view
        $this->view->hash = \zinux\kernel\security\security::GetHashString(array(session_id()));
        # if not a POST req.
        if(!$this->request->IsPOST())
            # just show the view
            return;
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
            else
                # this is an error
                throw new \zinux\kernel\exceptions\invalideOperationException("Miss-configured input!!!");
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
}
