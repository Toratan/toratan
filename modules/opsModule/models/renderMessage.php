<?php
namespace modules\opsModule\models;

/**
* The modules\opsModule\models\renderMessage
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class renderMessage extends \zinux\kernel\model\baseModel {
    /**
     * The related view
     * @var \zinux\kernel\view\baseView
     */
    protected $view;
    public function __construct(\zinux\kernel\view\baseView $view) { $this->view = $view; $this->ommit_body = !count($this->view->messages); }
    public function __render_header() { if($this->ommit_body) return;
?>
<div class="conversations">
    <div class="topbar">
        <div class="pull-left">
            <a class="btn btn-default" href="<?php echo "#!/send/message/to/{$this->view->target_user->username}?".\zinux\kernel\security\security::__get_uri_hash_string(array($this->view->target_user->user_id), @$_SERVER["HTTP_REFERER"])?>" id="send-message">
                <span class="glyphicon glyphicon-retweet"></span> Reply
            </a>
        </div>
        <div class='pull-right conversation-options' style="margin-bottom: 10px;">
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class='glyphicon glyphicon-cog'></span>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right">
                    <li><a href="#" id="delete-messages-view">Delete Messages</a></li>
                    <li><a href="#" id="report-conv-view">Report Conversation</a></li>
                    <li class='divider'></li>
                    <li><a href="#" id="delete-conversation-view">Delete Conversation</a></li>
                </ul>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="topbar message-check" style="padding-bottom: 10px;">
        <label class="pull-left" style="margin-top: 10px">Select messages to delete</label>
        <div class="pull-right">
            <button class="btn btn-default" id="delete-messages-cancel">Cancel</button>
            <button class="btn btn-primary" id="delete-messages-action">Delete</button>
        </div>
        <div class="clearfix"></div>
    </div>
    <div id="topbar-sep"></div>
<?php
    }
    public function __render_messages() { if($this->ommit_body) return;
?>
    <?php $view = $this->view; ?>
    <?php
    $is_me_sending = function(\core\db\models\message $message) use($view) {
        return ($message->sender_id == $view->current_user->user_id);
    };
    $get_sender_name = function(\core\db\models\message $message) use($view, $is_me_sending) {
        if($is_me_sending($message))
            return $view->current_user->get_RealName_or_Username();
        return $view->target_user->get_RealName_or_Username();
    };
    $get_sender_username = function(\core\db\models\message $message) use($view, $is_me_sending) {
        if($is_me_sending($message))
            return $view->current_user->username;
        return $view->target_user->username;
    };
    $get_sender_link = function(\core\db\models\message $message) use($get_sender_name, $get_sender_username) {
?>
        <a href='/@<?php echo $get_sender_username($message) ?>'><strong><?php echo $get_sender_name($message); ?></strong></a>
<?php
    };
    $get_sender_avatar = function(\core\db\models\message $message) use($is_me_sending) {
        $avatar = array();
        if($is_me_sending($message))
            $avatar = \core\ui\html\avatar::get_avatar_link($this->view->current_user->user_id);
        else
            $avatar = \core\ui\html\avatar::get_avatar_link($this->view->target_user->user_id);
        return $avatar[0];
    };
?>    <?php $mgroups = array(); ?>
    <?php
        foreach($this->view->messages as $index => $message):
            if(!$index || $get_sender_name($this->view->messages[$index - 1]) != $get_sender_name($message))
                $mgroups[count($mgroups)][] = $message;
            else
                $mgroups[count($mgroups) - 1][] = $message;
        endforeach;
    ?>
    <?php foreach($mgroups as $group): $message = @$group[0];?>
    <div class="message-group">
        <div class="arrow arrow-<?php echo $is_me_sending($message) ? "right me-sending" : "left you-sending" ?>"></div>
        <mgroup class='<?php echo $is_me_sending($message) ? "me-sending" : "you-sending" ?>'>
            <div class='sender-info <?php echo $is_me_sending($message) ? "me-sending" : "you-sending" ?>'>
                <?php if($is_me_sending($message)) echo $get_sender_link($message); ?>
                <img src='<?php echo $get_sender_avatar($message) ?>'  width="50" height="50" class='image img-thumbnail img-responsive' style='margin-right: 3px;margin-left: 3px'/>
                <?php if(!$is_me_sending($message)) echo $get_sender_link($message); ?>
            </div>
            <div class="clearfix"></div>
            <div class="separator"></div>
            <?php foreach($group as $message): ?>
                <message id='message-<?php echo $message->message_id ?>'>
                    <mcontainer>
                        <mhead>
                            <div class="pull-left">
                                <input type="checkbox" class="message-check input" mid='<?php echo $message->message_id ?>'/>
                            </div>
                            <div class='send-date pull-right'>
                                <?php echo $message->created_at; ?>
                            </div>
                        </mhead>
                        <mbody  class="pull-left text-justify">
                            <p class="inline"><?php echo $message->message_data; ?></p>
                        </mbody>
                        <div class="clearfix"></div>
                    </mcontainer>
                </message>
            <?php endforeach; ?>
        </mgroup>
        <div class="clearfix"></div>
    </div>
    <?php endforeach; ?>
    <?php if($this->view->is_more): ?>
    <div id="load-older-msgs" class="list-group text-center">
        <a href="/messages/fetch_conversation/page/<?php echo $this->view->request->params["page"] + 1 ?>/c/<?php echo $this->view->cid ?>/u/<?php echo $this->view->target_user->user_id ?>?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array($this->view->cid, $this->view->target_user->user_id, session_id()), ($_SERVER["REQUEST_SCHEME"]."://".__SERVER_NAME__."/messages")) ?>" id="load-older-msgs-link" class="list-group-item load-older">
            <span class="glyphicon glyphicon-arrow-down"></span> Load Older Messages
        </a>
    </div>
    <?php endif; ?>
</div>
<?php
    }
    public function __render_css() { if($this->ommit_body) return;
?>
    <style type='text/css'>
        .conversations{width: 100%}
        .conversations #topbar-sep {padding-top: 55px;}
        .conversations .topbar{position: absolute;background-color: #FFF!important;border-bottom: 1px solid #e6e6e6; width: 92%;top: 50px}
        .message-check{display: none}
        mgroup{
            display: block;
            width: 68%;
            border: 1px solid #DDD;
            padding: 5px;
            margin-bottom: 10px;
        }
        mgroup .separator{border-bottom: 1px solid #ccc;margin: 10px;padding:0;display: block;}
        message{
            display: block;
            margin: 0px 10px 5px 10px;
            padding: 15px;
            padding-top: 5px;
        }
        message mbody {
            display: block;
            margin-top: 10px;
            width: 100%;
            font-size: small;
            word-break: break-all!important
        }
        message.send-date{}
        message mhead{display: inline; color: #AAA;font-size: smaller}
        .media > .me-sending,
        .media > .you-sending {
            margin-right: 10px;
        }
        .arrow-left {
            border-color: transparent #DDD;
            border-style: solid;
            border-width: 10px 10px 10px 0px;
            height: 0px;
            width: 0px;
        }
        .arrow-right {
            border-color: transparent #DDD;
            border-style: solid;
            border-width: 10px 0px 10px 10px;
            height: 0px;
            width: 0px;
        }
        .message-group.arrow-right{
            border-left: 0px;
        }
        .me-sending{
            float: right!important;
        }
        .you-sending{
            float: left!important;
        }
        @media screen and (min-width:0) and (max-width: 400px) {
            .topbar{width: 90%!important;}
            .topbar label{font-size: smaller}
            .topbar button{font-size: smaller}
        }
        @media screen and (min-width:410px) and (max-width: 500px) {
            .topbar{width: 88%!important}
        }
        @media screen and (min-width:0) and (max-width: 900px) {
            mgroup {width: 95%!important}
        }
    </style>
<?php
    }
    public function __render_js() { if($this->ommit_body) return;
?>
    <script src="/access/js/moment.min.js"></script>
    <script type='text/javascript'>
        $(document).ready(function(){
            var is_deleting_messages = function() {
                if(typeof(window.deleting_message) === "undefined") return false;
                return window.deleting_message;
            };
            var ini_messages_js = function() {
                $("a[href='#']").click(function(e){ e.preventDefault(); });
                $(".send-date").each(function(){
                    $(this).html(
                        moment($(this).html(), 'ddd, DD MMM YYYY HH:mm:ss ZZ').format("lll")
                    );
                });
                $.propHooks.checked = {
                    set: function(elem, value, name) {
                      var ret = (elem[ name ] = value);
                      $(elem).trigger("change");
                      return ret;
                    }
                };
                $(".message-check").change(function(){
                    if($(this).prop("checked"))
                        $(this).parents("message").css({"background-color": "#F1F2F7", "border": "1px solid #CCC"});
                    else
                        $(this).parents("message").css({"background-color": "transparent", "border": "0"});
                });
                $("message mbody").click(function(){
                    if(is_deleting_messages()) 
                        $(this)
                            .parents("message")
                                .find("input.message-check")
                                    .prop("checked", function() { return !$(this).prop("checked"); });
                });
                var load_more_msgs = function(e){
                    e.preventDefault();
                    $(this).data("initial_html", $(this).html());
                    $(this).html("Loading....");
                    $.ajax({
                        url: $(this).attr("href"),
                        type: "POST",
                        data: "ajax=1",
                        success:function(data){
                            $(data).replaceAll("#load-older-msgs");
                            ini_messages_js();
                        }
                    }).fail(function(xhr) {
                        setTimeout(function() { $("#load-older-msgs-link").html($("#load-older-msgs").data("initial_html"));window.open_errorModal(xhr.responseText, -1, true); }, 500);
                    });
                };
                $("#load-older-msgs-link").click(load_more_msgs);
            };
            ini_messages_js();
            $("#delete-messages-view").click(function(){
                $('.message-check').fadeIn();$('.conversation-options').parents('.topbar').hide();
                window.deleting_message = true;
                $("message mbody").css("cursor", "pointer");
            });
            $("#delete-messages-cancel").click(function(){
                $('.conversation-options').parents('.topbar').show();
                $('.message-check').fadeOut();
                $("message mbody").css("cursor", "default");
                delete window.deleting_message;
                $(".message-check:checked").prop("checked", false);
            });
            $("a#send-message").click(function(e){
                e.preventDefault();
                var href = String($(this).attr('href')).split('#!')[1];
                if(typeof(href) === "undefined") return false;
                $.ajax({
                    type: "GET",
                    url: href+"&suppress_layout=1&ajax=1&inbox=1",
                    success:function(data) {
                        window.open_dialogModal(data);
                    }
                });
            });
            $("#delete-messages-action").click(function(){
                if($(".message-check:checked").length === 0) {
                    window.open_infoModal("<b>Select some message first!</b>", 1300);
                    return;
                }
                window.open_yesnoModal("<b>Once you delete your copy of selected message(s), it cannot be undone.<br />Are you sure?</b>", function(){
                    var ids = [];
                    $(".message-check:checked").each(function(){
                        ids.push($(this).attr("mid"));
                    });
                    var delete_messages_action = function(array){
                        if(typeof(array) === "undefined")
                            throw "expecting a list of IDs";
                        if(array.length === 0) return;
                        $.ajax({
                            url: "/messages/delete_messages/?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array(), $_SERVER["REQUEST_SCHEME"]."://".__SERVER_NAME__."/messages") ?>",
                            type: "POST",
                            data: {"messages[]":array},
                            success: function(data) {
                                if(data.length)
                                    window.open_infoModal(data);
                                $(".message-check:checked").each(function(){
                                    var $msg = $("message#message-"+$(this).attr('mid'));
                                    $msg.addClass("deleted");
                                    var $msgp = $msg.parent();
                                    if($msgp.children("message:not(.deleted)").length < 1)
                                        $msgp.parent(".message-group").slideUp('fast', function(){ $(this).remove(); });
                                    else
                                        $msg.fadeOut('slow', function(){ $(this).remove(); });
                                });
                            }
                        }).fail(function(xhr) {
                            setTimeout(function() { window.open_errorModal(xhr.responseText, -1, true); }, 500);
                        });
                    };
                    delete_messages_action(ids);
                });
            });
            $("#delete-conversation-view").click(function(){
                window.open_yesnoModal(
                    "<b>Once you delete your copy of conversation, it cannot be undone.<br />Are you sure?</b>",
                    function(){
                        $.ajax({
                            url: "/messages/delete_conversation?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array($this->view->cid), $_SERVER["REQUEST_SCHEME"]."://".__SERVER_NAME__."/messages"); ?>",
                            method: "POST",
                            data: "cid=<?php echo $this->view->cid; ?>",
                            success: function(){
                                window.open_dialogModal("<b>Conversation successfully deleted!");
                                setTimeout(function(){ window.location = "/messages"; }, 1400);
                            }
                        }).fail(function(xhr) {
                            setTimeout(function() { window.open_errorModal(xhr.responseText, -1, true); }, 500);
                        });
                    }
                );
            });
            $("#report-conv-view").click(function(){
                $.ajax({
                    url:"/messages/report/type/conv/i/<?php echo $this->view->cid; ?>?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array($this->view->cid), $_SERVER["REQUEST_SCHEME"]."://".__SERVER_NAME__."/messages") ?>",
                    type: "POST",
                    data: "ajax=1",
                    success: function(data) {
                        window.open_dialogModal(data);
                    }
                }).fail(function(xhr) {
                    setTimeout(function() { window.open_errorModal(xhr.responseText, -1, true); }, 500);
                });
            });
            $(document).ajaxStart(function(){ window.open_waitModal(); });
            $(document).ajaxStop(function(){ window.open_waitModal(true); });
        });
    </script>
<?php
    }
    public function __render_footer() { if(!$this->ommit_body) return;
?>
    <div class="list-group-item">
        <div class="list-group-item-heading text-center" style="font-variant: small-caps;font-weight: bolder">
            <span class='glyphicon glyphicon-warning-sign'></span> No Conversation Found!<br />
            <small class="text-muted" style='font-size: small'>You can only start a conversation directly from users' profile page.</small>
        </div>
    </div>
<?php
    }
}