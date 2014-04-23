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
    protected function plotOptions($active_type, $pid, $is_owner) {
        if(!$is_owner) return;
        ?>
<style>
    #directory-tree-opt {margin: -10px auto 10px auto;}
    #directory-tree-opt .btn{ zoom: 1; filter: alpha(opacity=80); opacity: 0.8; }
    #directory-tree-opt div.btn-group{ margin-right: 10px; }
    #directory-tree-opt .w60{ width: 60px; }
    #directory-tree-opt .btn-separator:after {
        content: ' ';
        display: block;
        float: left;
        background: #ADADAD;
        margin: 0 10px;
        height: 34px;
        width: 1px;
    }
</style>
<form id="opt-form" method="POST" action="/ops?<?php echo \zinux\kernel\security\security::GetHashString(array($active_type, $this->request->GetURI())) ?>">
    <input type="hidden" name="type" value="<?php echo $active_type ?>" />
    <input type="hidden" name="continue" value="<?php echo $this->request->GetURI() ?>" />
<div id="directory-tree-opt" class="btn-toolbar">
    <!-- Split button -->
    <div class="btn-group">
        <button type="button" class="btn btn-default" style="height: 34px"><input type="checkbox" class="input check-all"/></button>
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
      <ul class="dropdown-menu" role="menu">
          <li><a href="#" class="check-all" onclick="return false;">All</a></li>
        <li><a href="#" class="check-none" onclick="return false;">None</a></li>
        <li class="divider"></li>
        <li><a href="#" class="check-public" onclick="return false;">Public</a></li>
        <li><a href="#" class="check-private" onclick="return false;">Private</a></li>
      </ul>
    </div>
    <div class="unchecked-opt btn-group">
        <a href="<?php echo $this->request->GetURI(); ?>" class="btn btn-default w60"><span class="glyphicon glyphicon-refresh"></span></a>
        <?php if($this->tree_type == self::REGULAR) : ?>
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle w60" data-toggle="dropdown" title="Create New Item">
              <span class="glyphicon glyphicon-plus"></span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu">
              <li title="Create A Folder"><a href="#!/new/folder/?pid=<?php echo $pid, \zinux\kernel\security\security::GetHashString(array("folder", $pid,  session_id(),  \core\db\models\user::GetInstance()->user_id))?>" data-tag="folder" class="new-item"><span class="inline glyphicon glyphicon-folder-close"></span> Folder</a></li>
              <li title="Create A Note"><a href="#!/new/note/?pid=<?php echo $pid, \zinux\kernel\security\security::GetHashString(array("note", $pid,  session_id(),  \core\db\models\user::GetInstance()->user_id))?>" data-tag="note" class="new-item"><span class="inline glyphicon glyphicon-file"></span> Note</a></li>
              <li title="Create A Link"><a href="#!/new/link/?pid=<?php echo $pid, \zinux\kernel\security\security::GetHashString(array("link", $pid,  session_id(),  \core\db\models\user::GetInstance()->user_id))?>" data-tag="link" class="new-item"><span class="inline glyphicon glyphicon-link"></span> Link</a></li>
            </ul>
          </div>
        <?php endif; ?>
    </div>
    <div class="checked-opt checked-opt-unique btn-group hidden">
        <button type="submit" class="btn btn-default" title="Edit" name="ops" value="edit"><span class="glyphicon glyphicon-edit"></span></button>
    </div> <!--end .checked-opt -->
    <div class="checked-opt btn-group hidden">
<?php switch($this->tree_type):
    case self::REGULAR: ?>
            <button type="submit" class="btn btn-default w60" title="Archive" name="ops" value="archive"><span class="glyphicon glyphicon-save"></span></button>
<?php goto __GENERIC; break;
    case self::ARCHIVE: ?>
            <button type="submit" class="btn btn-default w60" title="Un-archive" name="ops" value="archive"><span class="glyphicon glyphicon-open"></span></button>
<?php goto __GENERIC; break;
    case self::SHARED:
__GENERIC: ?>
            <button type="submit" class="btn btn-default w60" title="Toggle Share" name="ops" value="share"><span class="glyphicon glyphicon-share-alt"></span></button>
            <button type="submit" class="btn btn-default w60" title="Delete" name="ops" value="trash"><span class="glyphicon glyphicon-trash"></span></button>
<?php break;
    case self::TRASH: ?>
    </div>
    <div class="btn-group checked-opt hidden">
        <button type="submit" class="btn btn-success w60" title="Restore" name="ops" value="restore"><span class="glyphicon glyphicon-cloud-upload"></span></button>
    </div>
    <div class="btn-group checked-opt hidden">
        <button type="submit" class="btn btn-danger w60" title="Restore" name="ops" value="remove"><span class="glyphicon glyphicon-remove"></span></button>
    </div>
    <div class="btn-group pull-right" style="padding-top: 3px;" >
        <a id="trash-detail-popover" data-container="body" data-toggle="popover" data-placement="left" data-content="<div style='text-align: justify;margin-bottom:-3px'>All items which have been labaled as trashed <b>will not</b> appear anywhere else; Though if they are marked as <u><i>archived</i></u> or <u><i>shared</i></u>. In order to make them available please <b>select and restore</b> them.</div>" data-original-title="" title="">
          <span class="glyphicon glyphicon-exclamation-sign"></span>
        </a>
        <script>$(document).ready(function() {$('#trash-detail-popover').popover({ trigger: 'hover', html: true }); });</script>
    </div>
    <div>
<?php break;
    default: throw new \zinux\kernel\exceptions\invalideOperationException("Undefined tree type# `{$this->tree_type}`!"); ?>
<?php endswitch; ?>
    </div> <!--end .checked-opt -->
    <div class="pull-right">
        <div class="btn-group hidden" id="ajax-loader-img" style="margin:0;padding:0">
            <img class="image" src="/access/img/ajax-loader.gif" />
        </div>
    </div>
</div> <!--end  menu-->
<div class="clearfix"></div>
<div id="ajax-placeholder" style=""></div>
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
                    echo "<li ".(strtoupper("{$active_type}s") == strtoupper($value)?"class='active'":"")."><a class='table-nav-link' href='/frame/e/trashes.$key'>$value</a></li>";
                    break;
                case self::ARCHIVE:
                    echo "<li ".(strtoupper("{$active_type}s") == strtoupper($value)?"class='active'":"")."><a class='table-nav-link' href='/frame/e/archives.$key'>$value</a></li>";
                    break;
                case self::SHARED:
                    echo "<li ".(strtoupper("{$active_type}s") == strtoupper($value)?"class='active'":"")."><a class='table-nav-link' href='/frame/e/shared.$key'>$value</a></li>";
                    break;
                default:
                    echo "<li ".(strtoupper("{$active_type}s") == strtoupper($value)?"class='active'":"")."><a class='table-nav-link' href='/frame/e/directory/$pid.$key'>$value</a></li>";
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
            $si .= "<span class='glyphicon glyphicon-trash' title='Deleted'></span>";
        if($item->is_archive)
            $si .= " <span class='glyphicon glyphicon-save' title='Archived'></span>";
        if(!strlen($si)) {
            switch($item->WhoAmI()) {
                case "note":
                    return "<span class='glyphicon glyphicon-file' title='Note'></span>";
                case "link":
                    return "<span class='glyphicon glyphicon-link' title='Link'></span>";
                case "folder":
                    return "<span class='glyphicon glyphicon-folder-close' title='Folder'></span>";
                default:
                    trigger_error("Undefined `{$item->WhoAmI()}` item ", E_USER_ERROR);
            }
        }
        return $si;
    }
    protected function getCheckBoxClasses(\core\db\models\item $item) {
        $cbc = "item-checkbox";
        if($item->is_public) $cbc .= " public-item";
        if(!$item->is_public) $cbc .= " private-item";
        return $cbc;
    }
    protected  function getStatusString(\core\db\models\item $item) {
        $s = "";
        $s .= ("&share=".($item->is_public?"0":"1"));
        $s .= ("&archive=".($item->is_archive?"0":"1"));
        $s .= ("&trash=".($item->is_trash?"0":"1"));
        return $s;
    }
    protected function plotTableRow(\core\db\models\item $item, $type, $parent_id, $is_owner) {
        if($item === NULL) {
            throw new \zinux\kernel\exceptions\invalideArgumentException("The item cannot be null...");
        }
?>
                <tr class="<?php echo $type ?>">
                    <td>
                        <?php if($is_owner) : ?><input name="items[]" class="input <?php echo $this->getCheckBoxClasses($item) ?>" related-item="<?php echo $item->WhoAmI();?>" type="checkbox" value="<?php echo $item->{"{$item->WhoAmI()}_id"}, $this->getStatusString($item), \zinux\kernel\security\security::GetHashString(array($item->WhoAmI(),  $item->{"{$item->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id)); ?>"/>
                        <?php else: ?>&nbsp;                        
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo $this->getStatusIcons($item) ?>
                    </td>
                    <td>
                        <a href='<?php echo $this->getNavigationLink($item); ?>' target='<?php echo $this->getNavigationTarget($item) ?>' onclick='window.top.document.title = "/ <?php echo $item->{"{$item->WhoAmI()}_title"}; ?>";'><?php echo $item->{"{$item->WhoAmI()}_title"}; ?></a>
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
    </div>
</form>
<hr />
<?php
    }
    protected function plotJS($type, $parent_id, $is_owner) {
?>
        <script type="text/javascript">
<?php
        if(isset($this->post_script) &&strlen($this->post_script)) :
            if(!is_string($this->post_script))
                throw new \zinux\kernel\exceptions\invalideArgumentException("Expecting the `post script` be a string!");
?>
            $(document).ready(function(){<?php echo $this->post_script; ?>});
<?php endif; if($is_owner): ?>
            window.update_menu_checkbox = function() {
                var all_checked = ($("input[type='checkbox'].item-checkbox:checked").length === $("input[type='checkbox'].item-checkbox").length);
                if($("input[type='checkbox'].item-checkbox:checked").length === 0) {
                    $("input[type='checkbox'].check-all").prop("indeterminate", false);
                    $("input[type='checkbox'].check-all").prop("checked", false);
                    $(".checked-opt").addClass("hidden");
                    $(".unchecked-opt").removeClass("hidden");
                } else {
                    $("input[type='checkbox'].check-all").prop("indeterminate", !all_checked);
                    $("input[type='checkbox'].check-all").prop("checked", all_checked);
                    $(".checked-opt").removeClass("hidden");
                    $(".unchecked-opt").addClass("hidden");
                    if($("input[type='checkbox'].item-checkbox:checked").length === 1)
                        $(".checked-opt.checked-opt-unique").removeClass("hidden");
                    else
                        $(".checked-opt.checked-opt-unique").addClass("hidden");
                }
            };
            window.reset_ajax_placeholder = function() { $("#ajax-placeholder").slideUp(function() { $(this).html("").hide().css("margin-bottom", "0px"); }); };
            $("input[type='checkbox'].item-checkbox").click(window.update_menu_checkbox);
            $(".check-all").click(function() {
                $("input[type='checkbox'].item-checkbox").prop("checked", !$("input[type='checkbox'].item-checkbox").prop("checked"));
                window.update_menu_checkbox();
            });
            $(".check-none").click(function() {
                $("input[type='checkbox'].item-checkbox").prop("checked", false);
                window.update_menu_checkbox();
            });
            $(".check-public").click(function() {
                $("input[type='checkbox'].public-item").prop("checked", true);
                $("input[type='checkbox'].private-item").prop("checked", false);
                window.update_menu_checkbox();
            });
            $(".check-private").click(function() {
                $("input[type='checkbox'].public-item").prop("checked", false);
                $("input[type='checkbox'].private-item").prop("checked", true);
                window.update_menu_checkbox();
            });
            $("a.new-item").click(function(e){
                switch($(this).attr("data-tag")) {
                    case "folder":
                    case "link":
                        e.preventDefault();
                        $.ajax({
                            url: $(this).attr('href').split("#!")[1]+"&suppress_layout=1&continue=<?php echo $this->request->GetURI(); ?>"
                        });
                        break;
                    default:
                }
            });
            $("button[value='edit']").click(function(e) {
                e.preventDefault();
                $.ajax({
                    type: $('form#opt-form').attr("method"),
                    url: $('form#opt-form').attr("action").split("?")[0],
                    data: "ops=edit&"+$('form#opt-form').serialize()+$('form#opt-form').attr("action").split("?")[1]
                });
            });
            $( document ).ajaxStart(function() {
                window.reset_ajax_placeholder();
                $("#ajax-loader-img").removeClass("hidden");
            });
            $( document ).ajaxStop(function() {
                $("#ajax-loader-img").addClass("hidden");
            });
            $(document).ajaxComplete(function( event, xhr, settings ) {
                console.log(xhr.responseText);
                if(xhr.responseText.length === 0) window.reset_ajax_placeholder();
                else $("#ajax-placeholder").hide().html(xhr.responseText).css("margin-bottom", "10px").slideDown('slow');
            });
            window.reset_ajax_placeholder();
            window.update_menu_checkbox();
<?php endif; ?>
        </script>
<?php
    }
    protected  function plotItems($type, $collection, $parent_id, $is_owner) {
        if($is_owner)
            $this->plotOptions($type, $parent_id, $is_owner);
        $this->plotHeadTypes($type, $parent_id);
        $this->plotJS($type, $parent_id, $is_owner);
        if(!count($collection)) {
?>
        <hr />
        <blockquote class="text-center text-muted" style="border-right: 5px #EEEEEE solid">No item found....</blockquote>
        <hr />
<?php
            return;
        }
        $this->plotTableHeader();
        foreach($collection as $folder)
        {
            $this->plotTableRow($folder, $type, $parent_id, $is_owner);
        }
        $this->plotTableFooter();
    }
    public function plotFolders($collection, $parent_id, $is_owner) {
        $this->plotItems("folder", $collection, $parent_id, $is_owner);
    }
    public function plotNotes($collection, $parent_id, $is_owner) {
        $this->plotItems("note", $collection, $parent_id, $is_owner);
    }
    public function plotLinks($collection, $parent_id, $is_owner) {
        $this->plotItems("link", $collection, $parent_id, $is_owner);
    }
}