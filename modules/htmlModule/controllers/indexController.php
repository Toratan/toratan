<?php
namespace modules\htmlModule\controllers;
/**
 * The modules\htmlModule\controllers\indexController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class indexController extends \modules\defaultModule\controllers\indexController
{
    public function __construct()
    {
        \ob_start();
    }
    public function Initiate()
    {
        parent::Initiate();
        $view = new \zinux\kernel\mvc\view(
            $this->request->view->name,
            new \zinux\kernel\mvc\action(
                $this->request->action->name,
                new \zinux\kernel\mvc\controller(
                    $this->request->controller->name,
                    new \zinux\kernel\mvc\module("default")
                    )));
        $this->view->metadata = &$view;
        $this->layout = new \zinux\kernel\layout\baseLayout($this->view);
        $this->layout->meta = new \zinux\kernel\mvc\layout("default", $view->relative_module);
        $this->layout->request->module = new \zinux\kernel\mvc\module("default");
        $this->layout->SetLayout("default");
    }
    public function IndexAction()
    {
        parent::IndexAction();
    }
    public function Dispose()
    {
        $this->view->suppressView();
        $content = \ob_get_clean();
        \trigger_error("Find a good url relocater and noscript remover regex!");
        $content = \preg_replace("#<noscript>(.*)<\/noscript>#im", "", $content);
        echo $content;
        parent::Dispose();
    }
}