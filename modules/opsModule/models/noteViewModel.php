<?php
namespace modules\opsModule\models;
/**
* The modules\opsModule\models\noteViewModel
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class noteViewModel
{
    /**
     * The related view to note
     * @var \zinux\kernel\view\baseView
     */
    protected $view;
    /**
     * Construct a note view
     * @param \zinux\kernel\view\baseView $view The related view to note
     */
    public function __construct(\zinux\kernel\view\baseView &$view) {
        $this->view = $view;
    }
    /**
     * Render the notes
     * @param boolean $is_preview <b>(optional, default:  false)</b> Should render as preview?
     * @throws \zinux\kernel\exceptions\invalidArgumentException if `view->route` is undefined
     */
    public function __render($is_preview = false) {
?>
<?php
    if(!isset($this->view->route) || !is_array($this->view->route))
        throw new \zinux\kernel\exceptions\invalidArgumentException("`route` no provied");
    $writer = \core\db\models\user::find($this->view->instance->owner_id);
    $n = $this->view->instance;
    $this->view->is_archive = $n->is_archive;
    $this->view->is_trash = $n->is_trash;
    $this->view->layout->AddMeta("description", $n->note_title);
    $this->view->layout->addTitle($n->note_title);
    $tags = array();
    if($is_preview)
        $tags = $this->view->tags;
    else
        $tags = $n->tags;
    $author_link = "/profile/{$writer->user_id}";
    $cURL = preg_replace("#^/ops#i", "", $this->view->request->getURI());
    $is_owner = (\core\db\models\user::IsSignedin()  && $writer->user_id == \core\db\models\user::GetInstance()->user_id);
    $get_options_links = function(\core\db\models\note $note, $type, $cURL) use($is_preview){
        if($is_preview) return "#";
        $uri = '';
        switch(strtolower($type)) {
            case "delete":
                $uri =
                    "/delete/".
                    $note->WhoAmI()."/".$note->{"{$note->WhoAmI()}_id"}.
                    "/trash/".($note->is_trash ? \core\db\models\item::DELETE_PERIOD: \core\db\models\item::DELETE_PUT_TARSH).
                    "?".  \zinux\kernel\security\security::__get_uri_hash_string(array($note->WhoAmI(), $note->{"{$note->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id));
                    break;
            case "restore":
                $uri =
                    "/delete/".
                    $note->WhoAmI()."/".$note->{"{$note->WhoAmI()}_id"}.
                    "/trash/".(\core\db\models\item::DELETE_RESTORE).
                    "?".  \zinux\kernel\security\security::__get_uri_hash_string(array($note->WhoAmI(), $note->{"{$note->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id));
                    break;
            case "edit":
                $uri = 
                    "/edit/".
                    $note->WhoAmI()."/".$note->{"{$note->WhoAmI()}_id"}.
                    "?".  \zinux\kernel\security\security::__get_uri_hash_string(array($note->WhoAmI(), $note->{"{$note->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id));
                    break;
            case "archive":
                $uri = 
                    "/archive/".
                    $note->WhoAmI()."/".$note->{"{$note->WhoAmI()}_id"}.
                    "/archive/".($this->view->is_archive ? 0 : 1).
                    "?".  \zinux\kernel\security\security::__get_uri_hash_string(array($note->WhoAmI(), $note->{"{$note->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id));
                    break;
            case "share":
                $uri = 
                    "/share/".
                    $note->WhoAmI()."/".$note->{"{$note->WhoAmI()}_id"}.
                    "/share/".($note->is_public ? 0 : 1).
                    "?".  \zinux\kernel\security\security::__get_uri_hash_string(array($note->WhoAmI(), $note->{"{$note->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id));
                    break;
            default: throw new \zinux\kernel\exceptions\invalidArgumentException("Undefined type `$type`.");
        }
        return $uri = "$uri&continue=$cURL";
    };
?>
<style>
    table#title {margin:0; margin-top: -10px; margin-bottom: 5px;}
    table#title #headline{font-weight: 700;font-size: 140%;font-family: Baskerville, “Baskerville Old Face”, “Hoefler Text”, Garamond, “Times New Roman”, serif;}
    table#title tr td{border: 0;padding: 0}
    topic-meta {font-size: 80%;line-height: 1.42857143;color: #999;}
    #note-body {padding: 10px; text-align: justify}
    @media screen 
        and (min-width : 0px) 
        and (max-width : 500px) {
            table#title #headline{text-align: left!important}
    }
    .breadcrumb>li+li.no-bc-slash::before {content:''!important}
</style>
<script type="text/javascript">
<?php if($is_preview): ?>
    $(document).ready(function() {
        $("#note-render a")
                .removeAttr('href', '#')
                .removeAttr('target')
                .attr('title', 'Disabled')
                .css('cursor', 'pointer')
                .click(function() {
                    window.open_infoModal("<span class='glyphicon glyphicon-eye-open'></span> This is just a demo, links won't work!");
                });
    });
<?php endif; ?>
<?php if($is_owner) : ?>
    window.movement_callback = function(address){
        window.close_modal();
        if(address.length === 0) { setTimeout(function(){ window.open_errorModal("Couldn't fetch the proper address!"); }, 500); return; }
        var $epb = $("#note-render .breadcrumb").find("li:not(.cd)").remove().end();
        address.reverse().forEach(function(e) {$epb.prepend($("<li>").append($("<a>").attr("data-id", e.data_id).text(e.title).attr("href", "/#!/d/"+e.data_id+(e.is_active ? ".notes" : ".folders"))));});
    };
    function change_path() {
        <?php $profile =\core\db\models\profile::getInstance(); ?>
        <?php $s = $profile->getSetting("/general/directory-tree-sort-type"); ?>
        <?php $is_valid_s = ($s && is_array($s) && count($s) === 2); ?>
        <?php $s = ($is_valid_s ? $s : array("defaultHeadIndex" => 2, "defaultHeadOrder" => 0)); ?>
        $.ajax({
            type: "GET",
            url: "/ops/move?init=1&pid=<?php echo $n->parent_id ?>",
            data: "type=note&items[]=<?php echo itemInfo::encode($n) ?>&sort=<?php echo $s["defaultHeadIndex"] - 1?>&order=<?php echo $s["defaultHeadOrder"] ?>"+<?php echo json_encode(\zinux\kernel\security\security::__get_uri_hash_string(array("note", $n->parent_id))) ?>,
            success: function(data){
                window.top.open_dialogModal(data);
            }
        }).fail(function(xhr){
            setTimeout(function() { window.top.open_errorModal(xhr.responseText, -1, true); }, 500);
        }).always(function(){
            window.top.open_waitModal(true);
        });
    };
<?php endif; ?>
</script>
<div id="note-render">
    <ol class="breadcrumb">
        <?php $count = 0; foreach($this->view->route as $folder) : $active = count($this->view->route) == ++$count; $should_link = ($is_owner && strlen($folder->folder_title)); ?>
            <li <?php echo $active?"class='active'":""?>><?php echo $should_link ? "<a href='/#!/d/{$folder->folder_id}.".(!$active?"folders":"notes")."'>":"", $folder->folder_title, $should_link ? "</a>" : "" ?></li>
        <?php endforeach;unset($count);?>
            <?php if($is_owner): ?>
            <li class="pull-right no-bc-slash cd"><a href="#" onclick="change_path();return false;" data-toggle="tooltip" title="Change the path where the note will is saved.">Change</a></li>
            <?php endif; ?>
    </ol>
    <table class="table table-responsive " id="title">
        <tbody>
            <tr>
                <?php list($avatar, $def_avatar) = \core\ui\html\avatar::get_avatar_link($writer->user_id); ?>
                <td rowspan="2" style="width: 80px;height: 80px;">
                    <a href='<?php echo $author_link ?>' rel='author' target='__blank'>
                        <img src="<?php echo $avatar ?>" onerror="this.src='<?php echo $def_avatar ?>'" class="image img-responsive img-thumbnail" style="width: 90%"/>
                    </a>
                </td>
                <td colspan="2" class='text-justify' id="headline" style='line-height: initial;word-break: break-all;'>
                    <?php echo $n->note_title ?>
                </td>
            </tr>
            <tr>
                <td style='padding-top: 5px;' class='text-justify'>
                    <topic-meta>
                        <?php 
                            $dt = new \modules\frameModule\models\directoryTree($this->view->request);
                            echo $dt->getStatusIcons($n);
                            unset($dt);
                        ?>
                        &mdash;&mdash;
                        Written by
                        <a href='<?php echo $author_link ?>' rel='author' target='__blank'>
                            <?php echo $writer->get_RealName_or_Username() ?>
                        </a>
                        &angmsd;
                        <?php $dt = $n->updated_at ?>
                        <abbr title="<?php echo $dt; ?>" class="initialism timeago" style='cursor: pointer!important'>
                            <time datetime="<?php echo $dt; ?>">
                                <?php echo $dt; ?>
                            </time>
                        </abbr>
                        <?php unset($dt); ?>
                    </topic-meta>
                </td>
                <td class="pull-right">
                <?php if($is_owner) : ?>
                    <div class="pull-right" id='owner-options'>
                        <div class="input-group inline">
                            <div class="btn-group">
                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
                                    <span class="glyphicon glyphicon-cog"></span> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li>
                                        <a href="<?php echo $get_options_links($n, 'edit', $cURL) ?>" style="color:#111100">
                                            <span class='glyphicon glyphicon-edit'></span> Edit
                                        </a>
                                    </li>
                                    <?php if($this->view->is_trash): ?>
                                    <li class='divider'></li>
                                    <li>
                                        <a href="<?php echo $get_options_links($n, 'restore', $cURL) ?>">
                                            <span class='glyphicon glyphicon-cloud-upload'></span> Restore
                                        </a>
                                    </li>
                                    <?php else: ?>
                                    <li class='divider'></li>
                                    <li>
                                        <a href="<?php echo $get_options_links($n, 'share', $cURL) ?>">
                                            <span class='glyphicon glyphicon-share-alt'></span> <?php echo $n->is_public?"Un-":""?>Share
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $get_options_links($n, 'archive', $cURL) ?>">
                                            <span class='glyphicon glyphicon-save'></span> <?php echo $this->view->is_archive?"Un-":""?>Archive
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    <li class='divider'></li>
                                    <li>
                                        <a href="<?php echo $this->view->is_trash ? "#!": "" /* we add # to delete permanent op. for fail-safe and only launch if a notice dialog pop-out by JS*/,
                                                $get_options_links($n, 'delete', $cURL); ?>" class='<?php echo $this->view->is_trash?"delete-permanent":""?>' style='color:#777'>
                                            <span class='glyphicon glyphicon-trash'></span> <?php echo $this->view->is_trash?"Delete Permanent":"Put to trash" ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="2">
                    <ul class="pagination" style='margin: 0;'>
                <?php if(count($tags)): ?>
                    <?php foreach($tags as $tag): ?>
                        <li>
                            <a href="/tag/<?php echo urldecode($tag->tag_value); ?>/list">
                                <span class='glyphicon glyphicon-tag small'></span> <?php echo $tag->tag_value; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                        <li><a href='#'>Untagged</a></li>
                <?php endif; ?>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>
    <hr style='margin: 0'/>
    <div class='row'>
        <div id="note-body" class="links-enabled pull-lefta col-lg-8 col-md-8 col-sm-8">
        <?php 
            # if note's pre-processed html body exists and not empty?
            # don't render the origin body, just echo the pre-processed one!
            # otherwise render the note's origin body.
            echo isset($n->note_html_body) && strlen($n->note_html_body) ? $n->note_html_body : self::__renderText($n->note_body); 
        ?>
            <div class="clearfix"></div>
            <?php self::__renderComments($n); ?>
        </div>
        <div class='col-lg-4 col-md-4 col-sm-4 apull-right'>
            <div class='visible-xs' style='margin-top: -200px'></div>
            <?php self::__renderSideBar($n); ?>
        </div>
    </div>
    <script src="/access/js/moment.min.js"></script>
    <link rel="stylesheet" href='/access/google-code-prettify/tomorrow-night.theme.min.css' />
    <script type="text/javascript" src="/access/google-code-prettify/prettify.js"></script>
    <script type="text/javascript">
        (function(){
    <?php if(!$is_preview): ?>
            window.ajax_start = function(){ window.open_waitModal();};
            window.ajax_stop = function() { window.open_waitModal(true);};
            window.ajax_error = function (event, jqXHR, settings, exception) { window.open_errorModal(jqXHR.responseText, -1, true); };
            $(window).ajaxStart(window.ajax_start);
            $(window).ajaxStop(window.ajax_stop);
            $(window).ajaxError(window.ajax_error);
    <?php endif; ?>
            window.update_time = function() {
                $('abbr.timeago').each(function(){
                    var sTime = $(this).find('time').attr("datetime");
                    var time = moment(sTime).format("lll");
                    var time_str = (moment(sTime).fromNow("lll")) + " ago";
                    $(this)
                        .attr('title', 'Updated at : ' + time)
                        .children('time')
                        .attr('title', $(this).attr('title'))
                        .addClass(".time-inited")
                        .html(time_str);
                });
            };
            if(typeof(moment) !== "undefined")
                setInterval(window.update_time, 500);
            else console.error("`moment` not defined");
        })(jQuery);
        $(document).ready(function(){
            window.update_time();
            prettyPrint();
    <?php if(!$is_preview): ?>
            $("#owner-options [role='menu'] a.delete-permanent").click(function(e) {
                e.preventDefault();
                // for fail-safe
                var href = String($(this).attr('href')).split("#!")[1];
                window.open_yesnoModal("Are you sure do you want to delete this note <b>permanently</b>?<br /><b class='text-muted'><span class='glyphicon glyphicon-info-sign'></span> This operation cannot be un-done.</b>", function(){
                    window.location = href;
                }, undefined, false);
            });
    <?php endif; ?>
        });
    </script>
</div>
<?php unset($get_options_links); ?>
<?php
    }
    public static function __renderSideBar(\core\db\models\note $note) {
        if(!@$note->is_public) return;
        $n = $note;
?>
    <div class="right-side-bar">
        <div class='author-popular-posts-container'>
            <legend style='margin-bottom: 0!important'>
                <a href='/@<?php echo $n->user->username ?>'><?php echo ($name = $n->user->get_RealName_or_Username(0)) ?></a>'<?php echo strtolower(substr($name, -1)) === 's' ? "" : "s"?> popular posts
            </legend>
            <center style='margin-top: 10px;'><img src='/access/img/config-loader.gif' id='confing-loader'/></center>
            <div class='author-popular-posts'></div>
            <script type="text/javascript">
                (function(){
                    <?php $s = $n->fetchStatusBits(); ?>
                    $.ajax({
                        global: false,
                        url: "/fetch/popular/type/notes?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array("notes", $n->note_id, $n->owner_id, $s))?>",
                        data: {
                            id: <?php echo json_encode($n->note_id); ?>,
                            uid: <?php echo json_encode($n->owner_id); ?>,
                            s: <?php echo json_encode($s); ?>
                        },
                        dataType: "JSON",
                        success: function(data) {
                            console.log(data.items.pop());
                            if(typeof(data.items) === "undefined")
                                throw "invalid data reception";
                            $(".author-popular-posts-container #confing-loader").fadeOut(function(){ 
                                var $c = $(".author-popular-posts");
                                data.items.forEach(function(item){
                                    var $pn = 
                                        $("<div>").addClass("popular-note").attr({"data-id": item.id})
                                        .append(
                                            $("<a>").attr({"href": item.url.replace(/^#!/ig, "")}).text(item.title)
                                                .append($("<time>").attr("datetime", item.created).addClass("populate-momentize")))
                                        .append($("<a>").attr("href", '#').text("Head Lines").addClass('show-summary'))
                                        .append($("<div>").addClass("clearfix"))
                                        .append($('<div>').html(item.summary).hide().addClass("summary"))
                                        .append($("<div>").addClass("clearfix"));
                                    $c.append($pn);
                                });
                                $(".show-summary")
                                    .click(function(){
                                        $(".summary.f").slideUp();
                                        $(this).parents('.popular-note').find('.summary').slideToggle(function(){$(this).toggleClass('f');});
                                        return false;
                                    });
                                $(".populate-momentize").each(function(){
                                    $(this).html(moment(moment($(this).attr("datetime")).format("ll"), "lll").format("MMM DD, YYYY")).removeClass("populate-momentize");
                                });
                            });
                        }
                    }).fail(function(){
                        $(".author-popular-posts-container #confing-loader").fadeOut(function(){ 
                            $(".author-popular-posts").append("<div class='text-muted text-center'>Failed to load popular posts!!!</div>");
                        });
                    }).always(function(){
                        $(".author-popular-posts-container #confing-loader").fadeOut();
                    });
                })(jQuery);
            </script>
        </div>
    </div>
</div>
<style type="text/css">
    .popular-note *{word-break: keep-all;overflow: hidden}
    .popular-note {display: block;font-size: small!important}
    .popular-note a{display:  block;padding:10px;padding-bottom: 0px}
    .popular-note time {color:#666;float: right;margin-top: 10px;margin-bottom: 10px}
    .popular-note .show-summary{margin-top: -10px;margin-bottom: 10px}
    .popular-note .summary  {text-align: justify;padding: 10px; border-left: 5px solid #eee;margin: 10px;color: #999}
    .popular-note a:hover{text-decoration: none;}
    .popular-note:hover{background-color: #F8F8F8;}
    .popular-note:not(:last-child) {border-bottom: 1px solid #e6e6e6;}
    .right-side-bar {margin-top: 13px; display: block;}
    @media screen and (max-width: 500px) {
        #note-bodya{width: 100%!important;clear: both}
        .right-side-bar.sticked{ position: static!important }
    }
    @media screen and (min-width: 500px) and (max-width: 900px) {
        .author-popular-posts-container legend {font-size: small!important;font-weight: bold}
    }
    .right-side-bar {min-height: 300px;padding:10px}
</style>
<link rel="stylesheet" href='/access/css/social/share.css' />
<script type="text/javascript" src="/access/css/social/share.js"></script>
<?php
    }
    public static function __renderComments(\core\db\models\note $note) {
        $comments =\core\db\models\comment::__fetch_top($note->getItemID());
        $count_of_comments = \core\db\models\comment::__fetch_count($note->getItemID());
        $rc = new renderComment($note->getItemID(), ($note->owner_id == @\core\db\models\user::GetInstance()->user_id), $comments, $count_of_comments);
        $rc->__render_global_header();
        $rc->__render_css();
        $rc->__render_new_comment();
        $rc->__render_prev_comments_header();
        $rc->__render_prev_comments();
        $rc->__render_prev_comments_footer();
        $rc->__render_js();
        $rc->__render_global_footer();
    }
    public static function __renderText($text, $echo = 1) {
        (new \vendor\markdown\Ciconia\CiconiaInitializer())->Execute();
        $ciconia = new \Ciconia\Ciconia();
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\FencedCodeBlockExtension());
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\TaskListExtension());
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\InlineStyleExtension());
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\WhiteSpaceExtension());
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\TableExtension());
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\UrlAutoLinkExtension());
        $text = $ciconia->render($text);
        if($echo)
            echo $text;
        else return $text;
    }
}