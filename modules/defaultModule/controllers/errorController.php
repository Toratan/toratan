<?php
namespace modules\defaultModule\controllers;
    
/**
 * The modules\defaultModule\controllers\errorController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class errorController extends \zinux\kernel\controller\baseController
{
    public function Initiate()
    {
        parent::Initiate();
        $this->layout->SetLayout("error");
        $mp = new \zinux\kernel\utilities\pipe("__ERRORS__");
        if(!$mp->hasFlow()) $this->view->error[] = new \Exception("!!NO ERROR!!");
        while($mp->hasFlow()) {
            $this->view->error[] = unserialize($mp->read());
        }
        $this->layout->AddTitle("Oops!");
    }
    /**
    * The modules\defaultModule\controllers\errorController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
        if(count($this->view->error)) {
            $last = end($this->view->error);
            /** 
             * status codes reference : RFC 2616
             * http://tools.ietf.org/html/rfc2616
             */
            switch(true) {
                case $last instanceof \zinux\kernel\exceptions\invalidCookieException:
                case $last instanceof \zinux\kernel\exceptions\invalidArgumentException:
                case $last instanceof \zinux\kernel\exceptions\invalidOperationException:
                    /* Bad Request */
                    $code = 400;
                    break;
                case $last instanceof \zinux\kernel\exceptions\securityException:
                case $last instanceof \zinux\kernel\exceptions\accessDeniedException:
                case $last instanceof \zinux\kernel\exceptions\permissionDeniedException:
                    /* Unauthorized */
                    $code = 401;
                    break;
                case $last instanceof \zinux\kernel\exceptions\notFoundException:
                case $last instanceof \core\db\exceptions\dbNotFoundException:
                    /* Not Found */
                    $code = 404;
                    break;
                default: 
                    /* Intername Server Error */
                    $code  = 500;
                    break;
                case $last instanceof \zinux\kernel\exceptions\notImplementedException: 
                    /* Not Implemented */
                    $code = 501; 
                    break;
            }
            $this->view->setView($code);
            header("HTTP/1.1 $code");
        }
    }
}
