<?php
namespace modules\opsModule\controllers;
    
/**
 * The modules\opsModule\controllers\profileController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class profileController extends \zinux\kernel\controller\baseController
{
    const PROFILE_NOT_CREATED = -1;
    const PROFILE_SKIPPED = 0;
    const PROFILE_CREATED = 1;
    /**
    * The modules\opsModule\controllers\profileController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        $profile_status = \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id)->getSetting("/profile/status") ;
        # if profile not already created
        if(!$profile_status || $profile_status == self::PROFILE_NOT_CREATED)
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
                # purging un-necessary indexes
                unset($this->request->POST['finish']);
                # save on session cache socket
                $sc->save("step#$current_step", $this->request->POST);
                $this->submitProfile($sc);
                \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id)->setSetting("/profile/status", self::PROFILE_CREATED);
                header("location: /profile");
                exit;
            # are we going to skip?
            case isset($this->request->params['skip']):
                throw new \zinux\kernel\exceptions\notImplementedException("SKIP NOT IMPLEMENTED");
                break;
        }
        # save on session cache socket
        $sc->save("step#$current_step", $this->request->POST);
    }
    /**
     * Submits a profile data saved on a session cache
     * @param \zinux\kernel\caching\sessionCache $session_cache profile data container
     * @throws \zinux\kernel\exceptions\invalideArgumentException if $session_cache is NULL
     */
    private function submitProfile(\zinux\kernel\caching\sessionCache $session_cache)
    {
        if(!$session_cache)
            throw new \zinux\kernel\exceptions\invalideArgumentException("NULL cache passed!!");
        # do whatever you want
        echo "<h1>Submiting profile with values: </h1>";
        \zinux\kernel\utilities\debug::_var($session_cache->fetchAll());
        $profile = \core\db\models\profile::getInstance(\core\db\models\user::GetInstance()->user_id);
        foreach($session_cache->fetchAll() as $value)
        {
            if(!\is_array($value))
                throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid input! profile was not created....");
            foreach($value as $key=> $_value)
            {
                if(!\is_array($_value))
                    $profile->$key = $_value;
                else
                {
                    $profile->$key = \implode(";", $_value);
                }
            }
        }
        $profile->save();
    }
}
