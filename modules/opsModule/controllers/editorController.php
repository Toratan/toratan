<?php
namespace modules\opsModule\controllers;
    
/**
 * The modules\opsModule\controllers\editorController
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class editorController extends \zinux\kernel\controller\baseController
{
    /**
     * Redirects header to pointed URL
     * @param string $this->request->params["continue"] if $this->request->params["continue"]
     * provided it will set the header location to the point, otherwise redirects to site's root
     */
    protected function Redirect() {
        $params = $this->request->params;
        if(headers_sent())
            return false;
        if(isset($params["continue"]))
        {
            header("location: {$params["continue"]}");
            exit;
        }
        return false;
    }
    /**
    * The modules\opsModule\controllers\editorController::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction() { throw new \zinux\kernel\exceptions\invalidOperationException; }
    /**
    * @access via /editor/change/editor/to/{ace|classic}?continue=(URI)
    * The \modules\opsModule\controllers\editorController::changeAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function changeAction() {
        \zinux\kernel\security\security::IsSecure($this->request->params, array("to", "continue"));
        $this->request->params["continue"] = preg_replace("#&ui=\w+#i", "", $this->request->params["continue"]);
        $this->request->params["continue"] .= "&ui={$this->request->params["to"]}";
        if($this->request->IsGET()) {
            $this->Redirect();
            exit;
        }
        \zinux\kernel\security\security::IsSecure($this->request->params, array("submit-type"));
        switch(strtolower($this->request->params["submit-type"]))
        {
                case "change-editor":
                    break;
                default: throw new \zinux\kernel\exceptions\invalidOperationException;
        }
        $this->initEditorBuffer($this->request->params);
        $this->Redirect();
        exit;
    }
    /**
     * Init editor buffer with it's standard data format
     * @param array $data the data for fill, should always contain {"{$item_type}_title", "{$item_type}_body", "pid"}.
     * @param string $item_type should be one of {note|folder|link}
     * @throws \zinux\kernel\exceptions\accessDeniedException if user not signed in current session
     */
    protected function initEditorBuffer(array $data, $item_type = "note") {
        switch(strtolower($item_type)) {
            case "note": break;
            default: throw new \zinux\kernel\exceptions\invalidArgumentException("`$item_type` not defined");
        }
        if(!\core\db\models\user::IsSignedin())
            throw new \zinux\kernel\exceptions\accessDeniedException;
        \zinux\kernel\security\security::IsSecure($data, array("{$item_type}_title", "{$item_type}_body", "pid"));
        $sc = new \zinux\kernel\caching\sessionCache("editor-buffer");
        $sc->deleteAll();
        if(!isset($data["owner_id"]))
            $data["owner_id"] = \core\db\models\user::GetInstance()->user_id;
        if(strtolower(@$data["to"]) === "ace")
            list(, $data["{$item_type}_body"]) =\core\db\models\note::__normalize("", $data["{$item_type}_body"], 1); 
        $sc->save("buffer",$data);
    }

    /**
    * The \modules\opsModule\controllers\editorController::optionsAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function optionsAction() {
        \zinux\kernel\security\security::IsSecure($this->request->params, array('type'));
        $type = $this->request->params["type"];
        unset($this->request->params["type"]);
        switch(strtolower($type)) {
            case "ace": case "classic": break;
            default: throw new \zinux\kernel\exceptions\invalidArgumentException("undefined `$type` editor.");
        }
        $this->layout->SuppressLayout();
        $class = "\\modules\\opsModule\\models\\{$type}EditorOptions";
        $this->view->current_options = new $class;
        $profile =\core\db\models\profile::getInstance();
        if($this->request->IsGET()) {
            $this->view->themes = glob("./access/rte/ace/src-min-noconflict/theme-*");
            if(($opt = $profile->getSetting("/rte/options/$type"))) {
                if($opt instanceof \modules\opsModule\models\EditorOptions)
                    $this->view->current_options = $opt;
            }
        } else {
            if(!count($this->request->params))
                throw new \zinux\kernel\exceptions\invalidArgumentException;
            $this->view->current_options->__parse($this->request->params);
            $profile->setSetting("/rte/options/$type", $this->view->current_options);
            echo "<span class='glyphicon glyphicon-ok'></span> Settings successfully saved.";
            exit;
        }
    }
}
