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
    public function plotOptions($active_type, $pid, $is_owner) {
        if(!$is_owner) return;
        ?>
<style>
    #directory-tree-opt {margin: -10px auto 10px auto;}
    #directory-tree-opt .btn{ zoom: 1; filter: alpha(opacity=80); opacity: 0.8; }
    #directory-tree-opt div.btn-group{ margin-right: 10px; }
    #directory-tree-opt .w60{ width: 60px; }
    #explorer-table { margin-top: 130px; }
</style>
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
<?php 
        $mp = new \core\utiles\messagePipe();
        $check = $mp->hasFlow();
        $msgs = "";
        if($check) $msgs = "<ol class='text-info' style='list-style-image: url(\"/access/img/bullet.png\");padding:0;margin-left:15px;'>";
        while(($msg = $mp->read())) $msgs .= "<li>$msg</li>";
        if($check) $msg = "</ol>";
        if($check) :
?>
<div id="ops-container-info">
    <script>
        $(document).ready(function(){
            setTimeout(function() {
                window.top.open_infoModal('<?php echo addslashes($msgs) ?>', -3200);
            }, 150);
        });
    </script>
</div>
<?php
        endif;
?>
<div id="ajax-placeholder" style="margin: 0;">
</div>
<div class="clearfix"></div>
<?php
    }
    public function plotHeadTypes($active_type, $pid) {
     $active_type = \ActiveRecord\Utils::pluralize($active_type);
?>
    <div class="visible-xs clearfix"></div>
    <div style="padding: 0% 0 1% 0;" id="directory-tree-headtypes">
        <ul class="nav nav-tabs" style="font-weight: 900;font-variant: small-caps"><?php
        foreach(array(
                "folders" => "<span class='glyphicon glyphicon-folder-".(strtoupper("{$active_type}") == "FOLDERS"?"open":"close")."'></span>", 
                "notes" => "<span class='glyphicon glyphicon-file'></span>", 
                "links" => "<span class='glyphicon glyphicon-link'></span>") as $key => $value)
        {
            switch($this->tree_type)
            {
                case self::TRASH:
                    echo "<li ".(strtoupper("{$active_type}") == strtoupper($key)?"class='active'":"")." title='".(ucwords($key))."'><a class='table-nav-link' href='/frame/e/trashes.$key'>$value</a></li>";
                    break;
                case self::ARCHIVE:
                    echo "<li ".(strtoupper("{$active_type}") == strtoupper($key)?"class='active'":"")." title='".(ucwords($key))."'><a class='table-nav-link' href='/frame/e/archives.$key'>$value</a></li>";
                    break;
                case self::SHARED:
                    echo "<li ".(strtoupper("{$active_type}") == strtoupper($key)?"class='active'":"")." title='".(ucwords($key))."'><a class='table-nav-link' href='/frame/e/shared.$key'>$value</a></li>";
                    break;
                default:
                    echo "<li ".(strtoupper("{$active_type}") == strtoupper($key)?"class='active'":"")." title='".(ucwords($key))."'><a class='table-nav-link' href='/frame/e/directory/$pid.$key'>$value</a></li>";
                    break;
            }
        }
        ?></ul>
    </div>
<?php
    }
    public function plotJS($type, $parent_id, $is_owner) {
?>
        <script type="text/javascript">
<?php if($is_owner): ?>
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
            window.reset_ajax_placeholder = function() { $("#ajax-placeholder").slideUp(function() { $(this).html("").hide().css("margin", "0px"); }); $("#explorer-table").animate({"margin-top": "130"});};
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
                        window.reset_ajax_placeholder();
                        // we need this delay to changes in `window.reset_ajax_placeholder()` have time to get applied
                        setTimeout(function($this) {
                            $.ajax({
                                url: $($this).attr('href').split("#!")[1]+"&suppress_layout=1&continue=<?php echo $this->request->GetURI(); ?>"
                            });
                        }, 400, this);
                        break;
                    default:
                }
            });
            window.ajax_start = function() {
                $("#ajax-placeholder *").prop("readonly", true);
                $("#ajax-loader-img").removeClass("hidden");
            };
            window.ajax_stop = function() {
                $("#ajax-loader-img").addClass("hidden");
            };
            window.ajax_error = function(){
                $("#ajax-placeholder *").prop("readonly", false).first().focus();
            };
            window.ajax_success = function( event, xhr, settings ) {
                console.log("ok from setup with data ");
                if(typeof(xhr.responseText) === "undefined" || xhr.responseText.length === 0) window.reset_ajax_placeholder();
                else {
                    $("#explorer-table").animate({"margin-top": "193"}, 'slow');
                    $("#ajax-placeholder")
                        .hide()
                        .html(xhr.responseText + "<small><a href='#' onclick='window.reset_ajax_placeholder();return false;'>Close</a></small>")
                        .css("margin", "-10px auto  10px auto")
                        .slideDown('slow');
                    setTimeout(function() { $("#ajax-placeholder input").first().focus(); }, 500);
                }
            };
            window.apply_change = function(apply_type, data) {
                switch(apply_type) {
                    case "name-editted":
                        $("tr td.item-name-selected").children("a").html(data);
                        break;
                    default:
                        throw "Undefined `"+apply_type+"`!";
                }
            };
            $(window).ajaxStart(window.ajax_start);
            $(window).ajaxStop(window.ajax_stop);
            $(window).ajaxError(window.ajax_error);
            $(window).ajaxSuccess(window.ajax_success);
            window.reset_ajax_placeholder();
            window.update_menu_checkbox();
<?php endif; ?>
        </script>
<?php
    }
    protected function plotTableHeader($active_type) {
?>
<script src="/access/js/moment.min.js"></script>
<script src="/access/js/jquery.tablesorter.min.js"></script>
<style>
    .ui-icon {
        width: 16px;
        height: 16px;
        background-image: url('/access/img/ui-icons.png');
        text-indent: -99999px;
        overflow: hidden;
        background-repeat: no-repeat;
    }
    table thead .fs-toggle {
        background-position: -128px -14px;
        display: inline-block;
        zoom: 1;
    }
    .header { cursor: pointer; }
    .header.headerSortUp .fs-toggle{
        background-position: -64px -14px;
        margin-bottom: -2px;
    }
    .header.headerSortDown .fs-toggle{
        background-position: -0px -14px;
        margin-bottom: -2px;
    }
    .header.headerSortDown,
    .header.headerSortUp {
        border-top: 3px solid #f3f3f3!important;
    }        
</style>
<form id="opt-form" method="POST" action="/ops?<?php echo \zinux\kernel\security\security::GetHashString(array($active_type, $this->request->GetURI())) ?>">
    <input type="hidden" name="type" value="<?php echo $active_type ?>" />
    <input type="hidden" name="continue" value="<?php echo $this->request->GetURI() ?>" />
    <div id="explorer-table" class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 0.1%"></th>
                    <th style="width: 67px;overflow: hidden" title="Status"><span class="glyphicon glyphicon-th-large"></span>&nbsp;<span class="ui-icon fs-toggle"></span></th>
                    <th style="width: 70%;">Name <span class="ui-icon fs-toggle"></span></th>
                    <th id="table-header-updated">Updated at <span class="ui-icon fs-toggle"></span></th>
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
        $counter = 0;
        if($item->is_public && ++$counter)
            $si .= "<span class='glyphicon glyphicon-share-alt' title='Shared'></span>";
        if($item->is_trash && ++$counter)
            $si .= "<span class='glyphicon glyphicon-trash' title='Deleted'></span>";
        if($item->is_archive && ++$counter)
            $si .= " <span class='glyphicon glyphicon-save' title='Archived'></span>";
        if($counter < 3) {
            switch($item->WhoAmI()) {
                case "note":
                    $si = "<span class='glyphicon glyphicon-file' title='Note'></span> $si"; 
                    break;
                case "link":
                    $si = "<span class='glyphicon glyphicon-link' title='Link'></span> $si";
                    break;
                case "folder":
                    $si = "<span class='glyphicon glyphicon-folder-close' title='Folder'></span> $si";
                    break;
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
    protected function getStatusBinary(\core\db\models\item $item) {
        return preg_replace("#[a-z&=]+#", "", $this->getStatusString($item));
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
        <tr class="<?php echo $type ?> item-row">
            <td>
                <?php if($is_owner) : ?><input name="items[]" class="input <?php echo $this->getCheckBoxClasses($item) ?>" related-item="<?php echo $item->WhoAmI();?>" type="checkbox" value="<?php echo $item->{"{$item->WhoAmI()}_id"}, $this->getStatusString($item), \zinux\kernel\security\security::GetHashString(array($item->WhoAmI(),  $item->{"{$item->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id)); ?>" onclick="window.update_menu_checkbox();"/><?php else: ?>&nbsp;<?php endif; ?></td>
            <td class="status" status="<?php echo $this->getStatusBinary($item) ?>">
                <?php echo $this->getStatusIcons($item) ?>
            </td>
            <td class="item-name">
                <a href='<?php echo $this->getNavigationLink($item); ?>' target='<?php echo $this->getNavigationTarget($item) ?>' onclick='window.top.document.title = "/ <?php echo $item->{"{$item->WhoAmI()}_title"}; ?>";'><?php echo $item->{"{$item->WhoAmI()}_title"}; ?></a>
            </td>
            <td class="updated-at" id="<?php echo $type, '-', $item->{"{$item->WhoAmI()}_id"}?>-updated" origin-date="<?php echo $item->updated_at?>"></td>
        </tr>
<?php
        $this->post_script .= "$(\"table tbody tr.$type td#$type-{$item->{"{$item->WhoAmI()}_id"}}-updated\").html(moment(moment('$item->updated_at').format('lll')).fromNow()).attr('title', moment('$item->updated_at').format('lll'));";
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
    protected function plotTableJS($active_type) {
        if(isset($this->post_script) && strlen($this->post_script)) :
            if(!is_string($this->post_script))
                throw new \zinux\kernel\exceptions\invalideArgumentException("Expecting the `post script` be a string!");
?>
        <script type="text/javascript">
            $(document).ready(function(){<?php echo $this->post_script; ?>
                $('table.table').tablesorter({
                    sortList: [[2,0]],
                    textExtraction: function(node) {
                        var txt = $(node).text();
                        if($(node).hasClass('updated-at')) {
                            txt = new Date($(node).attr('origin-date')).getTime();
                        }
                        if($(node).hasClass('status')) {
                            txt = $(node).attr('status') + $(node).next("td").text().trim();
                        }
                        return txt;
                    },
                    headers:{
                        0: { sorter: false }
                    }
                });
                $.propHooks.checked = {
                    set: function(elem, value, name) {
                      var ret = (elem[ name ] = value);
                      $(elem).trigger("change");
                      return ret;
                    }
                };
                $("button[name='ops']").click(function(e){
                    if($(this).attr('value') === 'edit') return;
                    e.preventDefault();
                    $('form#opt-form')
                            .prepend($("<input>").attr("type", "hidden").attr("name", "ops").val($(this).attr('value')))
                            .submit();
                });
                $("button[value='edit']").click(function(e) {
                    e.preventDefault();
                    if($("input[type='checkbox'].item-checkbox:checked").first() === 0) return;
                    $.ajax({
                        type: "GET",
                        url: "/ops/edit?<?php echo $active_type ?>="+$("input[type='checkbox'].item-checkbox:checked").first().val()+"&continue=<?php echo $this->request->GetURI(); ?>"
                    });
                });
                $("input[type='checkbox'].item-checkbox").change(function() {
                    if($(this).prop('checked')) {//f7fafc
                        $(this).parent().parent().css({"background-color": "#f7fbff"});
                        $(this).parent().siblings('.item-name').addClass("item-name-selected");
                    } else {
                        $(this).parent().parent().css("background-color", "transparent");
                        $(this).parent().siblings('.item-name').removeClass("item-name-selected");
                    }
                });
            });
        </script>
<?php endif; 
    }
    protected  function plotItems($active_type, $collection, $parent_id, $is_owner) {
        if(!count($collection)) {
?>
        <div id="explorer-table">
            <hr />
            <blockquote class="text-center text-muted" style="border-right: 5px #EEEEEE solid">No item found....</blockquote>
            <hr />
        </div>
<?php
            return;
        }
        $this->plotTableHeader($active_type);
        foreach($collection as $folder)
        {
            $this->plotTableRow($folder, $active_type, $parent_id, $is_owner);
        }
        $this->plotTableFooter();
        $this->plotTableJS($active_type);
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