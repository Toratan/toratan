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
            case "no-notif":
                $uri = "/notifications/stop/{$note->WhoAmI()}/{$note->getItemID()}?".\zinux\kernel\security\security::__get_uri_hash_string(array($note->WhoAmI(), $note->getItemID(), session_id()));
                break;
            case "ay-notif":
                $uri = "/notifications/start/{$note->WhoAmI()}/{$note->getItemID()}?".\zinux\kernel\security\security::__get_uri_hash_string(array($note->WhoAmI(), $note->getItemID(), session_id()));
                break;
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
        change_path.pid = address[0].data_id;
        $("#note-render .breadcrumb a").click(function(){window.location = $(this).attr('href');});
    };
    function change_path() {
        <?php $profile =\core\db\models\profile::getInstance(); ?>
        <?php $s = $profile->getSetting("/general/directory-tree-sort-type"); ?>
        <?php $is_valid_s = ($s && is_array($s) && count($s) === 2); ?>
        <?php $s = ($is_valid_s ? $s : array("defaultHeadIndex" => 2, "defaultHeadOrder" => 0)); ?>
        change_path.pid = <?php echo $n->parent_id ?>;
        $.ajax({
            type: "GET",
            url: "/ops/move?init=1&pid="+change_path.pid,
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
<?php if($is_owner) : ?>
    <ol class="breadcrumb">
        <?php $count = 0; foreach($this->view->route as $folder) : $active = count($this->view->route) == ++$count; $should_link = ($is_owner && strlen($folder->folder_title)); ?>
            <li <?php echo $active?"class='active'":""?>><?php echo $should_link ? "<a href='/#!/d/{$folder->folder_id}.".(!$active?"folders":"notes")."'>":"", $folder->folder_title, $should_link ? "</a>" : "" ?></li>
        <?php endforeach;unset($count);?>
            <?php if($is_owner): ?>
            <li class="pull-right no-bc-slash cd"><a href="#" onclick="change_path();return false;" data-toggle="tooltip" title="Change the path where the note will is saved.">Change</a></li>
            <?php endif; ?>
    </ol>
<?php else: ?>
    <div style="margin-top: 56px;"></div>
<?php endif; ?>
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
                        <?php $dt = $n->created_at ?>
                        <abbr title="<?php echo $dt; ?>" class="initialism timeago" style='cursor: pointer!important'>
                            <time datetime="<?php echo $dt; ?>">
                                <?php echo $dt; ?>
                            </time>
                        </abbr>
                        <?php unset($dt); ?>
                        <?php if($n->is_public || $n->vote_count) : ?>
                        <a href="#rate-note" class="text-muted" style="cursor: pointer">
                            <span class="glyphicon glyphicon-signal"></span> Rated: <strong><span class="vote_value"><?php echo $n->vote_value ?></span> / 5</strong> (<span class="vote_count"><?php echo $n->vote_count ?></span> votes cast )
                        </a>
                        <?php endif; ?>
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
                                    <li>
                                        <a href="<?php echo $get_options_links($n, ($n->get_notification?'no':'ay').'-notif', $cURL) ?>" style="color:#111100">
                                            <span class="fa fa-volume-<?php  echo $n->get_notification?'off':'up' ?>"></span> <?php echo $n->get_notification?'Stop':'Get' ?> Notifications
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
            <?php self::__renderRateButtons($n) ?>
            <?php self::__renderSocialButtons($n, $this->view->request->GetPrimaryURI(1)) ?>
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
                        .attr('title', 'Created at : ' + time)
                        .children('time')
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
        $n = $note;
        $is_owner = (\core\db\models\user::IsSignedin()  && $note->owner_id == \core\db\models\user::GetInstance()->user_id);
?>
<?php if(!@$note->is_public || $is_owner): ?>
<style type="text/css">
    #note-body{width: 100%!important;}
</style>
<?php return ; endif ?>
    <div class="right-side-bar">
        <div class='author-popular-posts-container popular-posts-container'>
            <legend style='margin-bottom: 0!important'>
                <a href='/@<?php echo $n->user->username ?>'><?php echo ($name = $n->user->get_RealName_or_Username(0)) ?></a>'<?php echo strtolower(substr($name, -1)) === 's' ? "" : "s"?> popular posts
            </legend>
            <center style='margin-top: 10px;'><img src='/access/img/config-loader.gif' id='confing-loader'/></center>
            <div class='author-popular-posts'></div>
        </div>
        <div class='global-popular-posts-container popular-posts-container'>
            <legend style='margin-bottom: 0!important'>
                Related popular posts
            </legend>
            <center style='margin-top: 10px;'><img src='/access/img/config-loader.gif' id='confing-loader'/></center>
            <div class='global-popular-posts'></div>
        </div>
        <script type="text/javascript">
            var fetch_popular =(function(_data, slice){
                $.ajax({
                    global: false,
                    url: _data.url,
                    data: {
                        id: <?php echo json_encode($n->note_id); ?>,
                        uid: _data.uid
                    },
                    dataType: "JSON",
                    success: function(data) {
                        if(data.length === 0) {
                            $(_data.class + "-container").fadeOut('fast', function(){$(this).remove();});
                            return;
                        }
                        if(typeof(data.items) === "undefined")
                            throw "invalid data reception";
                        $(_data.class + "-container #confing-loader").fadeOut(function(){ 
                            var $c = $(_data.class);
                            data.items.slice(slice[0], slice[1]).forEach(function(item){
                                var $pn = 
                                    $("<div>").addClass("popular-note").attr({"data-id": item.id})
                                    .append(
                                        $("<a>").attr({"href": item.url.replace(/^#!/ig, "")}).text(item.title)
                                            .append($("<time>").attr("datetime", item.created).addClass("populate-momentize")))
                                    .append($("<a>").attr("href", '#').text("Summary").addClass('show-summary'))
                                    .append($("<div>").addClass("clearfix"))
                                    .append($('<blockquote>').html(item.summary).hide().addClass("summary"))
                                    .append($("<div>").addClass("clearfix"));
                                $c.append($pn);
                            });
                            var slideshow = function() {
                                $(this).off('click');
                                var _this = this;
                                var _s = $(this).parents('.popular-note').find('.summary');
                                $(".summary.f").not(_s).slideUp().removeClass('f');
                                if(!$(_s).hasClass('f'))
                                    $(_s).addClass('f').slideDown(function(){
                                        $(_this).on('click', slideshow).attr('title', 'Hide summary').tooltip();
                                    });
                                else
                                    $(_s).removeClass('f').slideUp(function(){
                                        $(_this).on('click', slideshow).attr('title', 'Show summary').tooltip();
                                    });
                                return false;
                            };
                            $(_data.class + "-container .show-summary").on('click', slideshow);
                            $(_data.class + "-container .populate-momentize").each(function(){
                                $(this).html(moment(moment($(this).attr("datetime")).format("ll"), "lll").format("MMM DD, YYYY")).removeClass("populate-momentize");
                            });
                        });
                    }
                }).fail(function(){
                    $(_data.class + "-container #confing-loader").fadeOut(function(){ 
                        $(_data.class).append("<div class='text-muted text-center'>Failed to load popular posts!!!</div>");
                    });
                }).always(function(){
                    $(_data.class + "-container #confing-loader").fadeOut();
                });
            });
            fetch_popular({
                    class: '.author-popular-posts',
                    url: "/fetch/popular/type/notes?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array("notes", $n->note_id, $n->owner_id))?>",
                    uid: <?php echo json_encode($n->owner_id); ?>
            }, [0, 5]);
            fetch_popular({
                    class: '.global-popular-posts',
                    url: "/fetch/related?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array($n->note_id, \core\db\models\item::WHATEVER))?>",
                    uid: <?php echo json_encode(\core\db\models\item::WHATEVER) ?>
            }, [0, 10]);
        </script>
    </div>
</div>
<style type="text/css">
    .popular-posts-container {margin-bottom: 33px;}
    .popular-note *{word-break: keep-all;overflow: hidden}
    .popular-note {display: block;font-size: small!important}
    .popular-note a{display:  block;padding:10px;padding-bottom: 0px}
    .popular-note time {color:#666;float: right;margin-top: 10px;margin-bottom: 10px}
    .popular-note .show-summary{margin-top: -20px;margin-bottom: 10px;color:#aaa;width: content-box}
    .popular-note .summary  {padding: 10px; margin: 10px;color: #666}
    .popular-note blockquote.summary {font-family:Georgia,serif;font-style:italic;font-size:14px;position:relative}
    .popular-note blockquote.summary:before{display:block;content:"\201C";font-size:30px;position:absolute;left:0;top:-7px;color:#7a7a7a}
    .popular-note blockquote.summary:after{display:block;content:"\201D";font-size:30px;position:absolute;right:10px;bottom:-7px;color:#7a7a7a}
    .popular-note a:hover{text-decoration: none;}
    .popular-note:hover{background-color: #F8F8F8;}
    .popular-note:not(:last-child) {border-bottom: 1px solid #e6e6e6;}
    .right-side-bar {margin-top: 13px; display: block;min-height: 300px;padding:10px}
    @media screen and (min-width: 500px) and (max-width: 900px) {
        .author-popular-posts-container legend {font-size: small!important;font-weight: bold}
    }
</style>
<?php
    }
    public static function __renderRateButtons(\core\db\models\note $note) {
        # no rating if not signed in!!
        if(!\core\db\models\user::IsSignedin() || !$note->is_public || $note->owner_id == \core\db\models\user::GetInstance()->user_id) return;
        $nv = new \core\db\models\note_vote;
?>
<div class="block text-center rate-this" id="rate-note">
    <style type="text/css">
            .rate-this {margin-bottom: -20px;margin-top: 60px;}
            .rates .rate {margin: -1px;padding: 10px auto 10px auto;width: 15px;cursor: pointer;font-size: large;}
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            var mvote = <?php echo $nv->is_voted($note->note_id, \core\db\models\user::GetInstance()->user_id); ?>;
            var voting  = false;
            function init_stars() {
                if(voting) return;
                $(".rates .rate").each(function(index, elem) {
                    if(index < mvote)
                        $(elem).addClass("vote-marked");
                    else $(elem).removeClass("vote-marked");
                });
                update_vote_stars();
                $(".rates .unvote").remove();
                if(mvote)
                    $(".rates>:last").append("<small style='' class='unvote'><a href='#'>Un-vote</span></small>").click(function(e){ if(voting) return; e.preventDefault(); vote(0, $(this)); });
            }
            function vote(vote, $this) {
                $.ajax({
                    global: false,
                    beforeSend: function() { voting = true; update_vote_stars("#e8e8e8", true);$this.parent(".rates").children(".rate").css("cursor", "wait");},
                    complete: function() { voting = false;$this.parent(".rates").children(".rate").css("cursor", "pointer");},
                    url: "/vote/type/note/i/<?php echo $note->getItemID() ?>?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array("note", $note->getItemID(), \core\db\models\user::GetInstance()->user_id))?>",
                    data: {vote: $this.addClass("rated").prevAll(".rate").length + 1, o: vote},
                    dataType: "JSON",
                    success: function(data){
                        for(var propertyName in data) {
                            if(data[propertyName] === null)
                                data[propertyName] = 0;
                        }
                        if(typeof(data.voted) === "undefined" || typeof(data.vote) === "undefined" || typeof(data.vote_value) === "undefined" || typeof(data.vote_count) === "undefined"){
                            window.open_errorModal("Unable to get a proper respond from the server!");
                            return;
                        }
                        mvote = data.vote;
                        $this.addClass("rated");
                        $(".vote_value").text(data.vote_value);
                        $(".vote_count").text(data.vote_count);
                        setTimeout(function(){init_stars();}, 100);
                    }
                });
            }
            init_stars();
            $(".rates").hover(function(){}, function(){
                if(voting) return;
                init_stars();
            });
            $(".rates .rate").hover(function(){
                if(voting) return;
                $(this)
                        .addClass("vote-marked")
                            .prevAll(".rate")
                                .addClass("vote-marked");
                $(this).nextAll(".rate").removeClass("vote-marked");
                update_vote_stars();
            }, function(){
                if(voting) return;
                $(this)
                        .removeClass("vote-marked")
                            .prevAll(".rate")
                                .removeClass("vote-marked");
                $(this).nextAll(".rate").removeClass("vote-marked");
                update_vote_stars();
            }).click(function(){
                if(voting) return;
                vote(1, $(this), $);
            });
            function update_vote_stars(color, force){
                if(voting && (typeof(force) === "undefined" || !force)) return;
                if(typeof(color) === "undefined")
                    color = "#0088cc";
                $(".rates .rate.vote-marked span").each(function(){
                        $(this)
                                .css("color", color)
                                .removeClass("glyphicon-star-empty")
                                .addClass("glyphicon-star");
                });
                $(".rates .rate:not(.vote-marked) span").each(function(){
                        $(this)
                                .css("color", "initial")
                                .removeClass("glyphicon-star")
                                .addClass("glyphicon-star-empty");
                });
            };
        });
    </script>
    <ul class="list-inline rates">
    <?php for($i=0;$i<5;$i++): ?>
        <li class="rate">
            <span class="glyphicon glyphicon-star-empty"></span>
        </li>
    <?php endfor; ?>
        <li style="display: block!important" class="text-muted vote-statistic">
            <small>
                Rated: <strong><span class="vote_value"><?php echo $note->vote_value ?></span> / 5</strong> (<span class="vote_count"><?php echo $note->vote_count ?></span> votes cast)
            </small>
        </li>
    </ul>
</div>
<?php 
    }
    public static function __renderSocialButtons(\core\db\models\note $note, $uri) {
        if(!@$note->is_public) return;
        $n = $note;
?>
    <div class="social-sharing">
        <style type="text/css">
            .social-sharing {margin-bottom: -20px;margin-top: 40px}
            .share-buttons{list-style: none;}.share-buttons li{display: inline;}
            .share-buttons li a {color: #aaa!important;}
            .share-buttons li a:hover{color: #555!important;}
        </style>
        <ul class="share-buttons list-unstyled list-inline text-center">
            <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($uri) ?>&t=<?php echo urlencode($n->getItemTitle()) ?>" target="_blank" title="Share on Facebook"><i class="fa fa-facebook-square fa-2x"></i></a></li>
            <li><a href="https://twitter.com/intent/tweet?source=<?php echo urlencode($uri) ?>&text=<?php echo urlencode($n->getItemTitle()) ?>:%20<?php echo urlencode($uri) ?>&via=toratan" target="_blank" title="Tweet"><i class="fa fa-twitter-square fa-2x"></i></a></li>
            <li><a href="https://plus.google.com/share?url=<?php echo urlencode($uri) ?>" target="_blank" title="Share on Google+"><i class="fa fa-google-plus-square fa-2x"></i></a></li>
            <li><a href="http://www.tumblr.com/share?v=3&u=<?php echo urlencode($uri) ?>&t=<?php echo urlencode($n->getItemTitle()) ?>&s=" target="_blank" title="Post to Tumblr"><i class="fa fa-tumblr-square fa-2x"></i></a></li>
            <li><a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode($uri) ?>&description=<?php echo urlencode($n->getItemTitle()) ?>" target="_blank" title="Pin it"><i class="fa fa-pinterest-square fa-2x"></i></a></li>
            <li><a href="http://www.reddit.com/submit?url=<?php echo urlencode($uri) ?>&title=<?php echo urlencode($n->getItemTitle()) ?>" target="_blank" title="Submit to Reddit"><i class="fa fa-reddit-square fa-2x"></i></a></li>
            <li><a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($uri) ?>&title=<?php echo urlencode($n->getItemTitle()) ?>&summary=<?php echo urlencode(strlen($n->note_summary) ? $n->note_summary : $n->getItemTitle()) ?>&source=<?php echo urlencode($uri) ?>" target="_blank" title="Share on LinkedIn"><i class="fa fa-linkedin-square fa-2x"></i></a></li>
            <li><a href="http://wordpress.com/press-this.php?u=<?php echo urlencode($uri) ?>&t=<?php echo urlencode($n->getItemTitle()) ?>&s=<?php echo urlencode($n->getItemTitle()) ?>" target="_blank" title="Publish on WordPress"><i class="fa fa-wordpress fa-2x"></i></a></li>
            <li><a href="mailto:?subject=<?php echo urlencode($n->getItemTitle()) ?>&body=<?php echo urlencode($n->note_summary." : $uri") ?>" target="_blank" title="Email"><i class="fa fa-envelope fa-2x"></i></a></li>
        </ul>
        <link href="/access/css/font-awesome.min.css" rel="stylesheet">
        <script type="text/javascript">
            (function(){
                $(".share-buttons a:has(.fa)").click(function(e){
                    e.preventDefault();
                    window.open($(this).attr("href"), 'newwindow', 'width=600, height=400');
                });
            })(jQuery);
        </script>
    </div>
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