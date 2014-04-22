<?php
namespace modules\frameModule\models;
/**
* The modules\frameModule\models\directoryTree
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class directoryTree extends \stdClass
{
    const REGULAR = 0x1;
    const TRASH = 0x2;
    const ARCHIVE = 0x3;
    const SHARED = 0x4;
    protected $tree_type;
    /**
     * The request
     * @var \zinux\kernel\routing\request
     */
    protected $request;
    /**
     * The post script text container
     * @var string
     */
    protected $post_script;
    public function __construct (\zinux\kernel\routing\request $request, $__tree_type = self::REGULAR)
    {
        $this->request = $request;
        $this->tree_type = $__tree_type;
        $this->post_script = "";
    }
    protected function plotOptions($active_type, $pid) {
        ?>
<style>
    #directory-tree-opt .btn{ zoom: 1; filter: alpha(opacity=80); opacity: 0.8; }
    #directory-tree-opt div.btn-group{ margin-right: 10px; }
    #directory-tree-opt .w60{ width: 60px; }
</style>
<div style="margin:10px auto 20px auto" id="directory-tree-opt">
    <!-- Split button -->
    <div class="btn-group">
        <button type="button" class="btn btn-default" style="height: 34px"><input type="checkbox" class="input"/></button>
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
      <ul class="dropdown-menu" role="menu">
        <li><a href="#">All</a></li>
        <li><a href="#">None</a></li>
        <li class="divider"></li>
        <li><a href="#">Public</a></li>
        <li><a href="#">Private</a></li>
        <li class="divider"></li>
        <li><a href="#">Starred</a></li>
        <li><a href="#">Unstarred</a></li>
      </ul>
    </div>
    <div class="btn-group">
        <a href="<?php echo $this->request->GetURI(); ?>" class="btn btn-default w60"><span class="glyphicon glyphicon-refresh"></span></a>
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle w60" data-toggle="dropdown" title="Create New Item">
              <span class="glyphicon glyphicon-plus"></span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu">
              <li title="Create A Folder"><a href="#"><span class="inline glyphicon glyphicon-folder-close"></span> Folder</a></li>
              <li title="Create A Note"><a href="#"><span class="inline glyphicon glyphicon-file"></span> Note</a></li>
              <li title="Create A Link"><a href="#"><span class="inline glyphicon glyphicon-bookmark"></span> Link</a></li>
            </ul>
          </div>
    </div>
    <div class="btn-group hidden">
        <button type="button" class="btn btn-default w60" title="Archive"><span class="glyphicon glyphicon-floppy-disk"></span></button>
        <button type="button" class="btn btn-default w60" title="Toggle Share"><span class="glyphicon glyphicon-share-alt"></span></button>
        <button type="button" class="btn btn-default w60" title="Delete"><span class="glyphicon glyphicon-trash"></span></button>
    </div>
</div> <!--end  menu-->
<div class="clearfix"></div>
<?php
    }
    protected function plotHeadTypes($active_type, $pid) {
?>
    <div class="visible-xs clearfix"></div>
    <div style="padding: 0% 0 1% 0;" id="directory-tree-headtypes">
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
    protected function plotTableHeader() {
?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 0.1%"></th>
                    <th style="width: 0.1%">Status&nbsp;</th>
                    <th style="width: 70%;"><span style="padding: 0%"></span>Name</th>
                    <th id="table-header-updated">Updated at</th>
                </tr>
            </thead>
            <tbody>
<?php
    }
    protected function getNavigationLink(\core\db\models\item $item) {
        switch($item->WhoAmI()) {
            case "note":
                return "/view/note/{$item->note_id}";
            case "link":
                return "/goto/link/{$item->link_id}/".\zinux\kernel\security\hash::Generate($item->link_id, 1, 1);
            case "folder":
                return "/frame/e/directory/{$item->folder_id}.folders";
            default:
                trigger_error("Undefined `{$item->WhoAmI()}` item ", E_USER_ERROR);
        }
    }
    protected  function getNavigationTarget(\core\db\models\item $item) {
        switch($item->WhoAmI()) {
            case "note": return "_top";
            case "link": return "_blank";
            case "folder": return "_self"; 
            default:
                trigger_error("Undefined `{$item->WhoAmI()}` item ", E_USER_ERROR);
        }
    }
    protected function getStatusIcons(\core\db\models\item $item) {
        $si = "";
        if($item->is_public)
            $si .= "<span class='glyphicon glyphicon-share-alt' title='Shared'></span>";
        if($item->is_trash)
            $si .= "<span class='glyphicon glyphicon-trash' title='Deleted'></span> ";
        if($item->is_archive)
            $si .= "<span class='glyphicon glyphicon-floppy-disk' title='Archived'></span>";
        return $si;
    }
    protected function plotTableRow(\core\db\models\item $item, $type, $parent_id, $is_owner) {
        if($item === NULL) {
            throw new \zinux\kernel\exceptions\invalideArgumentException("The item cannot be null...");
        }
        $c = 1;
//        $item->is_public = $c;
//        $item->is_trash = $c;
//        $item->is_archive = $c;
?>
                <tr class="<?php echo $type ?>">
                    <td>
                        <input class="input item-checkbox" related-item="<?php echo $item->WhoAmI();?>" type="checkbox" />
                    </td>
                    <td>
                        <?php echo $this->getStatusIcons($item) ?>
                    </td>
                    <td>
                        <a href='<?php echo $this->getNavigationLink($item); ?>' target='<?php echo $this->getNavigationTarget($item) ?>'><?php echo $item->{"{$item->WhoAmI()}_title"}; ?></a>
                    </td>
                    <td id="<?php echo $type, '-', $item->{"{$item->WhoAmI()}_id"}?>-updated"></td>
                </tr>
<?php
        $this->post_script .= "$(\"table tbody tr.$type td#$type-{$item->{"{$item->WhoAmI()}_id"}}-updated\").html(new Date(Date.parse('$item->updated_at')).toLocaleString());";
    }
    protected function plotTableFooter() {
?>
            </tbody>
        </table>
        <hr />
<?php
        if(isset($this->post_script) &&strlen($this->post_script)) :
            if(!is_string($this->post_script))
                throw new \zinux\kernel\exceptions\invalideArgumentException("Expecting the `post script` be a string!");
?>
        <script type="text/javascript">
            $(document).ready(function(){<?php echo $this->post_script; ?>});
        </script>
<?php
        endif;
    }
    protected  function plotItems($type, $collection, $parent_id, $is_owner) {
        $this->plotOptions($type, $parent_id);
        if(!count($collection)) {
?>
        <hr />
        <blockquote class="text-center text-muted" style="border-right: 5px #EEEEEE solid">No item found....</blockquote>
        <hr />
<?php
            return;
        }
        $this->plotHeadTypes($type, $parent_id);
        $this->plotTableHeader();
        foreach($collection as $folder)
        {
            $this->plotTableRow($folder, $type, $parent_id, $is_owner);
        }
        $this->plotTableFooter();
    }
    public function plotFolders($collection, $parent_id, $is_owner) {
        $this->plotItems("folders", $collection, $parent_id, $is_owner);
    }
    public function plotNotes($collection, $parent_id, $is_owner) {
        $this->plotItems("notes", $collection, $parent_id, $is_owner);
    }
    public function plotLinks($collection, $parent_id, $is_owner) {
        $this->plotItems("links", $collection, $parent_id, $is_owner);
    }
}