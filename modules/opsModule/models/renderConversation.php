<?php
namespace modules\opsModule\models;
    
/**
* The modules\opsModule\models\renderConversation
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class renderConversation extends \zinux\kernel\model\baseModel {
    /**
     * The related view
     * @var \zinux\kernel\view\baseView
     */
    protected $view;
    public function __construct(\zinux\kernel\view\baseView $view) {
        $this->view = $view;
    }
    public function __render_css() {
?>
<style type="text/css">
    @media screen and (min-width: 0px) and (max-width: 400px) {
        /* on very small widths: */
        #conversations .row .col-xs-3 { display: none /* hide avatars */}
        #conversations .row .col-xs-9 { width: 100%!important; display:  block!important /* expand last message */}
    }
    #conversations .conversation *{max-height: 100px}
    #conversations .conversation.active {font-weight: bold;color:#FFF}
    #conversations .conversation .image.avatar {max-height: 80px!important; max-width: 80px!important}
    #conversations .conversation.unseen {border: 2px solid #0088cc}
    .window-height{min-height: 500px;overflow: auto;}
    .load-older {margin-bottom: 10px;font-weight: bold;font-size: 80%;color:#0088cc!important}
</style>
<?php
    }
    public function __render_header() {
        ?>
<div class="row">
    <div class="col-md-5 col-sm-6 col-xs-12" id='conversations'>
        <script type='text/javascript'>
            $(function(){
            <?php if(count($this->view->conv_users)) : ?>
                return;
            <?php endif; ?>
                // important this codes be right bellow under #conversations
                // start tag for UI performance purposes
                $("#conversations")
                        .data('width', $("#conversations").width())
                        .removeAttr("Class")
                        .addClass("col-md-12 col-sm-12 col-xs-12");
            });
        </script>
        <legend>Conversations</legend>
        <div class="list-group window-height">
<?php
    }
    public function __render_conversations() {
?>
            <?php foreach($this->view->conv_users as $index => $user): ?>
            <a href="#" class="list-group-item conversation <?php echo @$this->view->conv_last_message[$index]->is_read ? "seen" : "unseen"?>" conv-id="<?php echo $this->view->conv_ids[$index]->conversation_id ?>" target-href="/messages/fetch_conversation/c/<?php echo $this->view->conv_ids[$index]->conversation_id ?>/u/<?php echo $user->user_id ?>?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array($this->view->conv_ids[$index]->conversation_id, $user->user_id, session_id())) ?>">
                <div class="row">
                    <div class="col-md-3 col-sm-3 col-xs-3" style="max-width: 100px!important;padding-left: 5px;<?php echo !@$this->view->conv_last_message[$index]->is_read ? "margin-top:-10px" : "" ?>">
                        <?php list($avatar, $def_avatar) = \core\ui\html\avatar::get_avatar_link($user->user_id); ?>
                        <?php if(!@$this->view->conv_last_message[$index]->is_read): ?>
                            <small style="color: #0088cc;font-size: 80%;margin: 0px!important;margin-bottom: -10px;margin-right: 5px;font-weight: bold;width: 100%" class="pull-left text-center new-conv">New</small>
                        <?php endif; ?>
                        <img src="<?php echo $avatar ?>" class="image img-thumbnail img-responsive img avatar" onerror="this.src='<?php echo $def_avatar ?>'"/>
                    </div>

                    <div class="col-md-9 col-sm-9 col-xs-9" style="">
                                <h4 class="list-group-item-heading" style="word-break: break-all;padding-bottom: 3px">
                                    <span class='pull-left'><?php echo ucwords($user->first_name . " " . $user->last_name) ?></span>
                                    <time class=" list-group-item-text  pull-right small text-muted datetime" datetime="<?php echo @$this->view->conv_ids[$index]->last_conversation_at ?>"></time>
                                    <div class="clearfix"></div>
                                </h4>
                                <p class="list-group-item-text small text-muted" style="height: 45px;overflow: hidden;word-break: break-all;">
                                    <?php echo @$this->view->conv_last_message[$index]->message_data ?>
                                </p>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
            <?php if(@$this->view->is_more): ?>
            <div class="clearfix"></div>
            <a href="/messages/page/<?php echo $this->view->request->params["page"] + 1?>" class="list-group-item text-center load-older" id="load-older-conv">
                <span class="glyphicon glyphicon-arrow-down"></span> Load Older Conversations
            </a>
            <?php endif; ?>
            <?php if(!count($this->view->conv_users)) : ?>
            <div class="clearfix"></div>
            <div class="list-group-item">
                <div class="list-group-item-heading text-center" style="font-variant: small-caps;font-weight: bolder">
                    <span class='glyphicon glyphicon-warning-sign'></span> No Conversation Found!<br />
                    <small class="text-muted" style='font-size: small'>You can only start a conversation directly from users' profile page.</small>
                </div>
            </div>
            <?php endif; ?>
            <div class="clearfix"></div>
        </div>
    </div>
<?php
    }
    public function __render_footer(){
?>
    <?php if(count($this->view->conv_users)) : ?>
    <div class="col-md-7 col-sm-6 col-xs-12" id="conversation-data">
        <legend>&nbsp;</legend>
        <div class="window-height" id="conversation-data-inner">
            <div id="conv-load-ui" class="text-center" style="display: none;">
                <img src="/access/img/ajax-loader.gif" />
            </div>
            <div id="conv-messages-placeholder">
                <div class="text-center text-muted" style="font-weight: bolder;font-variant: small-caps">
                    No Conversation Selected
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php
    }
    public function __render_js(){
?>
<script type="text/javascript">
    $(function(){
        // modify frames' size on window resize event
        $(window).resize(function() {
            var $frames = $('.window-height');
            if($(window).height() >= 420)
                // re-size the frames
                $frames.css('height', ($(window).height() - 160)+'px');
            else
                $frames.css('height', '768px');
        });
        // init frames' size to window's size
        $(window).resize();
    });
</script>
<?php if(count($this->view->conv_users)) : ?>
<script src="/access/js/moment.min.js"></script>
<script type="text/javascript">
    var init_conversations_js = function() {
        var update_dates = function() {
            $(".datetime:not(.inited)").each(function(){
                $(this).html(
                        moment($(this).attr('datetime')).fromNow("lll") + " ago"
                ).addClass("inited");
            });
        };
        update_dates();
        $(".conversation:not(.inited)").click(function(e){
            $(this)
                .addClass("active")
                .siblings()
                    .removeClass("active");
            $("#conversations").animate({"width": $("#conversations").data('width')}, function(){
                $("#conversation-data").fadeIn('slow');
            });
            $("#conv-messages-placeholder").html('');
            $("#conv-load-ui").show();
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: $(this).attr("target-href"),
                global: false,
                success: function(data) {
                    $("#conv-load-ui").hide();
                    $("#conv-messages-placeholder").prepend(data);
                    if($this.hasClass("unseen")) {
                        var $msg_badge_elem = $("[href='/messages'] .badge");
                        var unseen_count = parseInt($msg_badge_elem.html());
                        if(unseen_count === 1) { $msg_badge_elem.remove(); }
                        else  {$msg_badge_elem.html(unseen_count - 1); }
                    }
                    $this.removeClass("unseen").addClass("seen").find(".new-conv").html("&nbsp;");
                    $('[data-toggle="tooltip"]').tooltip();
                }
            }).fail(function(xhr) {
                setTimeout(function() { window.open_errorModal(xhr.responseText, -1, true); }, 500);
            });
            e.preventDefault();
        });
        var load_more_conv = function(e){
            e.preventDefault();
            $(this).data("initial_html", $(this).html());
            $(this).html("Loading....");
            $.ajax({
                url: $(this).attr("href"),
                type: "POST",
                data: "ajax=1",
                global: false,
                success:function(data){
                    $(data).replaceAll("#load-older-conv");
                    init_conversations_js();
                }
            }).fail(function(xhr) {
                setTimeout(function() { $("#load-older-conv").html($("#load-older-conv").data("initial_html"));window.open_errorModal(xhr.responseText, -1, true); }, 500);
            });
        };
        $("#load-older-conv:not(.inited)").click(load_more_conv);
    };
    $(function(){
        init_conversations_js();
        $(document).ajaxStart(function(){ window.open_waitModal(); });
        $(document).ajaxStop(function(){ window.open_waitModal(true); });
    });
</script>
<?php endif; ?>
<?php 
    }
}