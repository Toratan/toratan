<?php
namespace modules\defaultModule\controllers;
    
/**
 * The modules\defaultModule\controllers\errorController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class errorController extends \zinux\kernel\controller\baseController
{
    public static $__EXTERN_ERROR = NULL;
    public function Initiate()
    {
        parent::Initiate();
        $this->layout->SetLayout("error");
        if(!self::$__EXTERN_ERROR) {
            $mp = new \zinux\kernel\utilities\pipe("__ERRORS__");
            if(!$mp->hasFlow() && strtolower($this->request->action->name) === "index") {
                header("location: /");
                exit;
            }
            # empty the pipe and only keep the last error up
            while($mp->hasFlow())
                $this->view->error =$mp->read();
        } else {
            $this->view->error = self::$__EXTERN_ERROR;
            self::$__EXTERN_ERROR = NULL;
        }
        $this->layout->AddTitle("Toratan");
    }
    /**
    * The modules\defaultModule\controllers\errorController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        $last = $this->view->error;
        /** 
         * status codes reference : RFC 2616
         * http://tools.ietf.org/html/rfc2616
         */
        switch(true) {
            case $last instanceof \InvalidArgumentException:
            case $last instanceof \zinux\kernel\exceptions\invalidCookieException:
            case $last instanceof \zinux\kernel\exceptions\invalidArgumentException:
            case $last instanceof \zinux\kernel\exceptions\invalidOperationException:
                $msg = "Bad Request";
                $code = 400;
                break;
            case $last instanceof \zinux\kernel\exceptions\securityException:
            case $last instanceof \zinux\kernel\exceptions\accessDeniedException:
            case $last instanceof \zinux\kernel\exceptions\permissionDeniedException:
                $msg = "Forbidden";
                $code = 403;
                break;
            case $last instanceof \zinux\kernel\exceptions\notFoundException:
            case $last instanceof \core\db\exceptions\dbNotFoundException:
                $msg = "Not Found ";
                $code = 404;
                break;
            default: 
                $msg = "Intername Server Error";
                $code  = 500;
                break;
            case $last instanceof \zinux\kernel\exceptions\notImplementedException: 
                $msg = "Not Implemented";
                $code = 501; 
                break;
        }
        # set proper view file
        $this->view->setView("e$code");
        $er = new \core\db\models\exception;
        try{
            # record the error and fetch the error-reference
            $this->view->eref = $er->record($last, $code);
        } catch(\Exception $e) {
            # ignore
            unset($e);
        }
        # if headers has not sent yet
        if(!headers_sent())
            # send the error header
            header("HTTP/1.1 $code $msg");
    }

    /**
    * The \modules\defaultModule\controllers\errorController::viewAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function viewAction()
    {
        if($this->request->CountIndexedParam() !== 1 )
            die(\core\ui\html\alert::Tout("BAD REQUEST", \core\ui\html\alert::PIPE_DANGER));
        try{
            $this->view->e = \core\db\models\exception::find($this->request->indexed_param[0]);
        } catch(\zinux\kernel\exceptions\notFoundException $_e) {
            $e = new \core\db\models\exception;
            $e->disableSave();
            $e->exception_id = $this->request->indexed_param[0];
            $e->exception_data =serialize($_e);
            $this->view->e = $e;
        }
        $this->layout->SetLayout("basic");
        $this->layout->AddTitle("Error #{$this->view->e->exception_id}");
    }
}
