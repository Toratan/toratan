<?php
namespace modules\authModule\controllers;
    
/**
 * The modules\authModule\controllers\authController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
abstract class authController extends \zinux\kernel\controller\baseController
{
    public function Initiate()
    {
        parent::Initiate();
        
        $request = $this->request;
        
        if(headers_sent())
        {
            echo "<div><b>Header has been sent, Please click on <a href='".(isset($request->params["continue"])?$request->params["continue"]:"/")."'>this</a> to redirect.</b></div>";
        }
        
        if(!isset($request->params["continue"]))
        {
            header("location: /");
        }
        else
        {
            header("location: ".$request->params["continue"]);
        }
    }
}
