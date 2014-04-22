<?php
namespace modules\frameModule\models;
    
/**
* The modules\frameModule\models\directoryTree
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class directoryTree
{
    const REGULAR = 0x1;
    const TRASH = 0x2;
    const ARCHIVE = 0x3;
    const SHARED = 0x4;
    protected $tree_type;
    public function __construct ($__tree_type = self::REGULAR)
    {
        $this->tree_type = $__tree_type;
    }
    protected function plotHeadTypes($active_type, $pid) {
?>
    <div class="visible-xs clearfix"></div>
    <div style="padding: 0% 0 1% 0;" >
        <ul class="nav nav-tabs" style="font-weight: 900;font-variant: small-caps"><?php
        foreach(array("folders" => "Folders", "notes" => "Notes", "links" => "Links") as $key => $value)
        {
            switch($this->tree_type)
            {
                case self::TRASH:
                    echo "<li ".(strtoupper($active_type) == strtoupper($value)?"class='active'":"")."><a class='table-nav-link' link-type='folder' href='/frame/e/trashes.$key'>$value</a></li>";
                    break;
                case self::ARCHIVE:
                    echo "<li ".(strtoupper($active_type) == strtoupper($value)?"class='active'":"")."><a class='table-nav-link' link-type='folder' href='/frame/e/archives.$key'>$value</a></li>";
                    break;
                case self::SHARED:
                    echo "<li ".(strtoupper($active_type) == strtoupper($value)?"class='active'":"")."><a class='table-nav-link' link-type='folder' href='/frame/e/shared.$key'>$value</a></li>";
                    break;
                default:
                    echo "<li ".(strtoupper($active_type) == strtoupper($value)?"class='active'":"")."><a class='table-nav-link' link-type='folder' href='/frame/e/directory/$pid.$key'>$value</a></li>";
                    break;
            }
        }
        ?></ul>
    </div>
<?php
    }
    public function plotFolders($collection, $parent_id, $is_owner) {
        $this->plotHeadTypes("folders", $parent_id);
        echo __METHOD__;
    }
    public function plotNotes($collection, $parent_id, $is_owner) {
        $this->plotHeadTypes("notes", $parent_id);
        echo __METHOD__;
    }
    public function plotLinks($collection, $parent_id, $is_owner) {
        $this->plotHeadTypes("links", $parent_id);
        echo __METHOD__;
    }
}