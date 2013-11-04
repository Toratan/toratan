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
        $this->layout->SuppressLayout();
    }
    /**
     * Redirects header to pointed URL
     * @param string $this->request->params["continue"] if $this->request->params["continue"] 
     * provided it will set the header location to the point, otherwise redirects to site's root
     */
    protected function Redirect()
    {
        $request = $this->rerequest;
        
        if(headers_sent())
        {
            echo "<div><b>Header has been sent, Please click on <a href='".(isset($request->params["continue"])?$request->params["continue"]:"/")."'>this</a> to redirect.</b></div>";
        }
        
        if(isset($request->params["continue"]))
            header("location: ".$request->params["continue"]);
        else
            header("location: /");
    }
}
