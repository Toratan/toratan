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
    $cURL = $this->view->request->getURI();
    $is_owner = (\core\db\models\user::IsSignedin()  && $writer->user_id == \core\db\models\user::GetInstance()->user_id);
    # if the note is in root?    
    if(count($this->view->route) > 1)
        # remove the root title(we don't want to have ROOT in html, it's pretty in this way)
        $this->view->route[0]->folder_title = "";
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
</script>
<div id="note-render">
    <ol class="breadcrumb">
        <?php $count = 0; foreach($this->view->route as $folder) : $active = count($this->view->route) == ++$count; $should_link = ($is_owner && strlen($folder->folder_title)); ?>
            <li <?php echo $active?"class='active'":""?>><?php echo $should_link ? "<a href='/#!/d/{$folder->folder_id}.".(!$active?"folders":"notes")."'>":"", $folder->folder_title, $should_link ? "</a>" : "" ?></li>
        <?php endforeach;unset($count);?>
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
    <div id="note-body" style="width: calc(100% - 100px)" class="links-enabled pull-left"><?php 
        # if note's pre-processed html body exists and not empty?
        # don't render the origin body, just echo the pre-processed one!
        # otherwise render the note's origin body.
        echo isset($n->note_html_body) && strlen($n->note_html_body) ? $n->note_html_body : self::__renderText($n->note_body); 
    ?>
        <div class="clearfix"></div>
        <?php self::__renderComments($n); ?>
    </div>
<?php if(@$n->is_public): ?>
    <div class="pull-right right-sticky-container">
        <ul class="social-sharing" style="">
            <?php  $uri = $_SERVER["REQUEST_SCHEME"]."://".__SERVER_NAME__.preg_replace("#^/ops#i", "", $this->view->request->GetURI()); ?>
            <li class="shareBtn sbMain">
                <a href="#">
                    <span class="sIcon icon-share"></span>  
                    SHARE
                </a>
            </li>
            <li class="shareBtn animatable" id="sbTwitter">
                <a href="http://twitter.com/share?text=<?php echo urlencode(trim(substr($n->note_title, 0, 140 - strlen($uri) - 4))."...") ?>&url=<?php echo $uri ?>" target="_blank" title="Share on Twitter">
                    <span class="sIcon icon-bird"></span>  
                    TWEET
                </a>
            </li>
            <li class="shareBtn animatable" id="sbFacebook">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $uri ?>" target="_blank" title="Share on Facebook">
                    <span class="sIcon icon-facebook-B"></span>
                    SHARE
                </a>
            </li>
            <li class="shareBtn animatable" id="sbGoogle">
                <a href="https://plus.google.com/share?url=<?php echo $uri ?>" target="_blank" title="Share on Google+">
                    <span class="sIcon icon-google-plus-B"></span>  
                    SHARE
                </a>
            </li>
        </ul>
    </div>
    <style>
        @media screen and (max-width: 450px) {
            #note-body{width: 100%!important}
            .right-sticky-container{clear: both}
        }
        .right-sticky-container { width: 100px; margin-top: 23px }
        .sticky-wrapper.is-sticky  .right-sticky-container { margin-top: 10px;}
    </style>
    <link rel="stylesheet" href='/access/css/social/share.css' />
    <script type="text/javascript" src="/access/js/sticky/jquery.sticky.min.js"></script>
    <script type="text/javascript" src="/access/css/social/share.js"></script>
<?php endif; ?>
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
<?php if(@$n->is_public): ?>
            $(".right-sticky-container").sticky();
<?php endif; ?>
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
    public static function __renderComments(\core\db\models\note $note) {
        $comments =\core\db\models\comment::__fetch_top($note->getItemID());
        $count_of_comments = \core\db\models\comment::__fetch_count($note->getItemID());
        $rc = new renderComment($note->getItemID(), $comments, $count_of_comments);
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