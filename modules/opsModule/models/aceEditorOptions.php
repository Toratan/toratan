<?php
namespace modules\opsModule\models;
    
/**
* The modules\opsModule\models\aceEditorOptions
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class aceEditorOptions extends \modules\opsModule\models\EditorOptions
{
    /**
     * @var string
     */
     public  $theme;
     /**
      * @var integer
      */
     public  $font_size;
     /**
      * @var integer
      */
     public  $tab_size;
     /**
      * @var boolean
      */
     public  $should_warp;
     /**
      * @var boolean
      */
     public  $should_highlight_line;
     /**
      * @var boolean
      */
     public  $should_show_line_no;
     /**
      * construct a editor options instance
      * @return \modules\opsModule\models\EditorOptions $this
      */
     public function __construct()
     {
         $this->theme = "ace/theme/twilight";
         $this->font_size = 16;
         $this->tab_size = 4;
         $this->should_highlight_line = true;
         $this->should_show_line_no = true;
         $this->should_warp = true;
         return $this;
    }
}