<?php
namespace modules\authModule\controllers;
    
/**
 * The modules\authModule\controllers\authController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
abstract class authController extends \zinux\kernel\controller\baseController
{
    public function Initiate() {
        parent::Initiate();
        $this->view->errors = array();
    }
    /**
     * Redirects header to pointed URL
     * @param string $this->request->params["continue"] if $this->request->params["continue"] 
     * provided it will set the header location to the point, otherwise redirects to site's root
     */
    protected function Redirect()
    {
        $params = $this->request->params;
        
        if(headers_sent())
        {
            echo "<div><b>Header has been sent, Please click on <a href='".(isset($params["continue"])?$params["continue"]:"/")."'>this</a> to redirect.</b></div>";
        }
        if(isset($params["continue"]))
            header("location: {$params["continue"]}");
        else
            header("location: /");
        exit;
    }
}
