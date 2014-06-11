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
                    "?".  \zinux\kernel\security\security::GetHashString(array($note->WhoAmI(), $note->{"{$note->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id));
                    break;
            case "restore":
                $uri =
                    "/delete/".
                    $note->WhoAmI()."/".$note->{"{$note->WhoAmI()}_id"}.
                    "/trash/".(\core\db\models\item::DELETE_RESTORE).
                    "?".  \zinux\kernel\security\security::GetHashString(array($note->WhoAmI(), $note->{"{$note->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id));
                    break;
            case "edit":
                $uri = 
                    "/edit/".
                    $note->WhoAmI()."/".$note->{"{$note->WhoAmI()}_id"}.
                    "?".  \zinux\kernel\security\security::GetHashString(array($note->WhoAmI(), $note->{"{$note->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id));
                    break;
            case "archive":
                $uri = 
                    "/archive/".
                    $note->WhoAmI()."/".$note->{"{$note->WhoAmI()}_id"}.
                    "/archive/".($this->view->is_archive ? 0 : 1).
                    "?".  \zinux\kernel\security\security::GetHashString(array($note->WhoAmI(), $note->{"{$note->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id));
                    break;
            case "share":
                $uri = 
                    "/share/".
                    $note->WhoAmI()."/".$note->{"{$note->WhoAmI()}_id"}.
                    "/share/".($note->is_public ? 0 : 1).
                    "?".  \zinux\kernel\security\security::GetHashString(array($note->WhoAmI(), $note->{"{$note->WhoAmI()}_id"}, session_id(), \core\db\models\user::GetInstance()->user_id));
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
        </tbody>
    </table>
    <hr style='margin: 0'/>
    <div id="note-body" style="width: 100%" class="links-enabled">
    <?php
        (new \vendor\markdown\Ciconia\CiconiaInitializer())->Execute();
        $ciconia = new \Ciconia\Ciconia();
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\FencedCodeBlockExtension());
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\TaskListExtension());
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\InlineStyleExtension());
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\WhiteSpaceExtension());
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\TableExtension());
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\UrlAutoLinkExtension());
        echo $ciconia->render($n->note_body);
    ?>
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
                    var format = 'ddd, DD MMM YYYY HH:mm:ss ZZ';
                    var time = moment(sTime, format).format("lll");
                    var time_str = (moment(sTime, format).fromNow("lll")) + " ago";
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
}