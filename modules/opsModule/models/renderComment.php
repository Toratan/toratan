<?php
namespace modules\opsModule\models;
/**
* The modules\opsModule\models\renderComment
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class renderComment
{
    protected $note_id;
    protected $comments;
    protected $count_of_comments;
    protected $is_more;
    protected $is_owner;
    /**
     * Construct a comment renderer
     * @param $note_id the note's ID which the comments are belong to
     * @param array $comments array of comments
     * @param integer $count_of_comments (default: NULL) The count of total comments, if NULL passed, it will set automatically to `count($comments)`.
     */
    public function __construct($note_id, $is_owner, array $comments, $count_of_comments = 0) {
        $this->note_id = $note_id;
        $this->comments = $comments;
        $this->is_owner = $is_owner;
        if(is_null($count_of_comments))
            $count_of_comments = count($comments);
        $this->count_of_comments = $count_of_comments;
        $this->is_more = (count($comments) < $count_of_comments);
    }
    public function __render_global_header() {
?>
<div class="row">
    <div class="col-md-push-2 col-md-8 comments-container">
<?php
    }
    public function __render_css() {
?>
<style type="text/css">.comments-container{padding:20px 40px;border-bottom: 1px solid #e6e6e6!important;margin-bottom: 200px;}.comments-container>:not(.clearfix){margin-bottom:10px}.comments-container .avatar img{-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px}.comments-container .total-comment-no{font-weight:700;font-size:large}.comments-container .total-comment-no label{font-weight:700;font-size:medium}.comments-container .prev-comment-area .comments-head{border-bottom:2px solid #EEE;margin-bottom:20px}.comments-container .prev-comment-area .comments-head li{width:70px;padding:5px}.comments-container .prev-comment-area .comments-head li a{text-decoration:none;font-weight:700;color:#7c7c7c}.comments-container .prev-comment-area .comments-head li.active a{font-weight:bolder;color:#000}.comments-container .prev-comment-area .comments-head li a{display:block}.comments-container .prev-comment-area .comments-head li:not(.active) a:hover{color:#5a5a5a}.comments-container .prev-comment-area .comments-head li:not(.active):hover{border-bottom:2px solid #fc4}.comments-container .prev-comment-area .comments-head li.active{border-bottom:2px solid #08c}.comments-container .prev-comment-area .comments-head li .careta{margin-top:10px;margin-left:3px}.comments-container .prev-comment-area .comments-head li:not(.active) .caret{visibility:collapse}.comments-container .form-control{-webkit-border-radius:0;-moz-border-radius:0;border-radius:0}.comments-container .prev-comment-area .comment{margin-bottom:30px}.comments-container .prev-comment-area .comment .comment-body{padding-left:30px}.comments-container .prev-comment-area .comment .comment-body .commenter-detail .commenter-link{font-weight:bolder;display:inline}.comments-container .prev-comment-area .comment .comment-body .commenter-detail .comment-date{display:inline;color:#AAA;font-weight:700;font-size:small}.comments-container .prev-comment-area .comment .comment-body .commenter-detail .comment-date::before{content:" . "}.comments-container .prev-comment-area .comment .comment-body .comment-data{margin:10px;overflow:auto}.comments-container .prev-comment-area .comment .comment-body .comment-footer{margin:0 10px}.comments-container .prev-comment-area .comment .comment-body .comment-footer .vote{text-decoration:none}.comments-container .prev-comment-area .comment .comment-body .comment-footer .vote:hover{font-weight:700}.comments-container .prev-comment-area .comment .comment-body .comment-footer .vote.vote-up.disabled{color:#99C499}.comments-container .prev-comment-area .comment .comment-body .comment-footer .vote.vote-down.disabled{color:#C49999}.comments-container .prev-comment-area .comment .comment-body .comment-footer .vote.disabled:not(.voted) {cursor: default}.comments-container .prev-comment-area .comment .comment-body .comment-footer>*{display:inline;padding:3px}.comments-container .prev-comment-area .comment .comment-body .comment-footer .divider{padding-top:-10px}.comments-container .prev-comment-area .comment .divider::after{content:"."}.comments-container .prev-comment-area .comment .actions .list-inline>li{padding-left: 0;padding-right: 0;}.comments-container .prev-comment-area .comment .actions a{text-decoration: none;color:#08c;padding:2px}.comments-container .prev-comment-area .load-more-comment-container{border:1px solid #ddd;height:45px;padding-top:10px}.comments-container .prev-comment-area .comment.my-comment{border-left:2px solid #08c;padding-left:10px;margin-left:-10px}@media screen and (min-width:0) and (max-width:399px){.comments-container .user-comment-erea .comment-signin-container .burden{display:none}}@media screen and (min-width:0) and (max-width:700px){.comments-container .prev-comment-area .comment .comment-body{padding-left:10px}}
.comments-container .prev-comment-area .comment .comment-body .comment-footer .vote.vote-up.voted {color:green;font-weight: bold;border-bottom: 2px solid green}
.comments-container .prev-comment-area .comment .comment-body .comment-footer .vote.vote-down.voted {color:#CC4444;font-weight: bold;border-bottom: 2px solid #CC4444}
.comments-container .deleting {opacity:0.5;-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";filter:alpha(opacity=50);}
</style>
<?php
    }
    public function __render_new_comment() {
?>
<?php if($this->count_of_comments) : ?>
<div  class="total-comment-no"><span class='no'><?php echo $this->count_of_comments; ?></span> <label><?php echo \ActiveRecord\Utils::pluralize_if($this->count_of_comments, "comment")?></label></div>
<?php endif; ?>
<div class="user-comment-erea" style="width: 100%">
    <div class="row">
<?php if(!\core\db\models\user::IsSignedin()): ?>
        <div class="text-center col-xs-12 comment-signin-container" style="border: 1px solid #ddd;height: 55px;padding:15px;font-weight: bold">
            <span class="glyphicon glyphicon-warning-sign"></span> <span class="burden">You must </span><a href="/signup">Signup</a> or <a href="/signin">Signin</a> to comment.
        </div>
<?php else: ?>
        <div class="hidden-xs col-sm-1 avatar">
            <?php list($avatar_uri , $def_avatar) = \core\ui\html\avatar::get_avatar_link(\core\db\models\user::GetInstance()->user_id); ?>
            <img src="<?php echo $avatar_uri ?>" onerror="this.src='<?php echo $def_avatar ?>'" height="50" width="50">
        </div>
        <div class="col-xs-12 col-sm-11">
            <textarea class="form-control" name="comment" style="margin-top: -3px;max-width: 100%" placeholder="Leave a comment...."></textarea>
            <small class="text-primary"><span class="glyphicon glyphicon-hand-right"></span> Press <code class="text-primary" style="font-weight: bold">Ctrl+Enter</code> to submit the comment.</small>
            <script src="/access/js/autosize/jquery.autosize.min.js" type="text/javascript"></script>
            <script type="text/javascript">
                (function(){
                    window.init_comment_txtbox = function($item, obj) {
                        if(typeof(obj.url) === "undefined")
                            throw "undefined url";
                        obj = $.extend(true, {}, obj, {
                            data: {
                                nid: <?php echo json_encode($this->note_id); ?>,
                            }, 
                            always: function(){}
                        });
                        $($item)
                            .autosize()
                            .keydown(function(e) {
                                if ((e.keyCode === 10 || e.keyCode === 13) && e.ctrlKey) {
                                    var $this = $(this);
                                    $this.attr('readonly', "true").css("cursor", "progress");
                                    $.ajax({
                                        global: false,
                                        url: obj.url,
                                        type: "POST",
                                        data: $.extend({}, obj.data, {c: $($item).val()}),
                                        success: obj.success
                                    }).fail(function(xhr){
                                        setTimeout(function() { window.open_errorModal(xhr.responseText, -1, true); }, 500);
                                    }).always(function(){
                                        obj.always();
                                        $this.removeAttr('readonly').css("cursor", "initial");
                                    });
                                }
                            });
                    };
                    window.init_comment_txtbox("textarea[name='comment']", {
                        url: "/comment/new?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array($this->note_id))?>",
                        success: function(data) {
                                $(data).hide().prependTo(".comments").fadeIn(1000);
                                var cc = $(".total-comment-no .no").text();
                                $(".total-comment-no .no").html(parseInt(cc) + 1);
                                if(cc > 2)
                                    $(".total-comment-no label").text("comments");
                                else
                                    $(".total-comment-no label").text("comment");
                                window.init_comments();
                            }
                        });
                })(jQuery);
            </script>
        </div>
<?php endif; ?>
    </div>
</div>
<div class="clearfix"></div>
<?php
    }
    public function __render_prev_comments_header() {
?>
<div class="prev-comment-area">
    <?php if($this->count_of_comments) : ?>
    <ul class="list-inline comments-head">
        <li class="active"><a href="#" class="top-comments" op="top">Top  <span class='caret'></span></a></li>
        <li><a href="#" class="all-comments" op="all">All <span class='caret'></span></a></li>
    </ul>
    <?php else: ?>
    <hr style="border-top: 1px solid #e6e6e6" />
    <?php endif; ?>
    <div class="comments">
<?php
    }
    public function __render_prev_comments() {
        $cuid = @\core\db\models\user::GetInstance()->user_id;
        $comments = $this->comments;
?>
<?php foreach($comments as $comment): ?>
<?php $vote_status = \core\db\models\comment_voter::__voter_exists($comment->comment_id, $cuid); ?>
<?php list($avatar_uri , $def_avatar) = \core\ui\html\avatar::get_avatar_link($comment->user_id); ?>
<div class="comment <?php echo $comment->user_id == $cuid ? "my-comment" : "" ?>" data-commenter="<?php echo sha1($comment->user_id) ?>" data-id="<?php echo $comment->comment_id ?>">
    <div class="row">
        <div class="hidden-xs col-sm-1 comment-header avatar">
            <img src="<?php echo $avatar_uri ?>" onerror="this.src='<?php echo $def_avatar ?>'" height="50" width="50">
        </div>
        <div class="col-xs-12 col-sm-11 pull-right comment-body">
            <div class="row">
                <div class="col-xs-12 commenter-detail">
                    <div class="commenter-link">
                        <?php $commenter = \core\db\models\user::GetInstance() ?>
                        <a href="/@<?php echo $commenter->username ?>"><?php echo $commenter->get_RealName_or_Username() ?></a>
                    </div>
                    <div class="comment-date"><time datetime="<?php echo $comment->created_at ?>" class="timeago"></time></div>
                    <div class="actions pull-right" style="margin-right: -10px">
                        <ul class="list-inline">
                        <?php if($cuid == $comment->user_id): ?>
                            <?php /* for comments newer than 1-day old edit option is available!! */ ?>
                            <?php if(date_diff($comment->created_at, new \ActiveRecord\DateTime)->format("%a") < 1): ?>
                            <li><a href="#" data-toggle="tooltip" title="Edit" class="edit-comment small" style="margin-bottom: -13px" op="edit"><span class="glyphicon glyphicon-pencil"></span></a></li>
                            <li><span class="divider"></span></li>
                            <?php endif; ?>
                            <li><a href="#" data-toggle="tooltip" title="Delete" class="delete-comment" op="delete">&Cross;</a></li>
                        <?php elseif($this->is_owner): ?>
                            <li><a href="#" data-toggle="tooltip" title="Report" class="report-comment" op="report"><span class="glyphicon glyphicon-warning-sign"></span></a></li>
                        <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-xs-12 comment-data"><?php echo $comment->comment ?></div>
                <div class="col-xs-12 comment-footer">
                    <a href="#" class="vote vote-up text-success <?php echo $vote_status === 1 ? "voted" : "" ?>">
                        <span class="vote-val vote-up-val"><?php echo $comment->vote_up ? $comment->vote_up : "" ?></span> <span class="glyphicon glyphicon-chevron-up"></span>
                    </a>
                    <div class="divider"></div>
                    <a href="#" class="vote vote-down text-danger <?php echo $vote_status === 0 ? "voted" : "" ?>">
                        <span class="vote-val vote-down-val"><?php echo $comment->vote_down ? $comment->vote_down : "" ?></span> <span class="glyphicon glyphicon-chevron-down"></span>
                    </a>
                    <div class="actions pull-right">
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<?php endforeach; ?>
<?php if(!count($comments)) : ?>
<div class="text-muted text-center" style="border: 1px solid #DDD;height: 45px;padding-top: 10px;font-weight: bolder" >
    No comment found!
</div>
<?php endif; ?>
<?php
    }
    public function __render_prev_comments_footer() {
?>
    </div>
    <?php if($this->count_of_comments) : ?>
    <div class='load-more-comment-container text-center <?php echo $this->is_more ? "" : "hidden"?>'>
        <a href="#" class="load-more-comment">Load more comments.</a>
    </div>
    <?php endif; ?>
</div>
<?php
    }
    public function __render_global_footer() {
?>
        <div class="clearfix"></div>
    </div>
</div>
<?php
    }
    public function __render_js() {
?>
<script type="text/javascript">
window.init_comments = function() {
    window.update_comment_times = function(){
        $("time.timeago").each(function(){
            var date = moment($(this).attr("datetime"));
            $(this)
                .html(date.fromNow("lll") + " ago")
                .attr('title', date.format("lll"))
                .attr('data-toggle', 'tooltip')
                .css("cursor", "pointer");
        });
    };
    $("textarea[name='comment']").val('');
    if(typeof(window.init_comments.uct) !== "undefined")
        clearInterval(window.init_comments.uct);
    window.init_comments.uct = setInterval(window.update_comment_times, 15000);
    window.update_comment_times();
    $("a[href=#]:not(.inited)").addClass("inited").click(function(e){e.preventDefault();$(this).blur();});
<?php if(\core\db\models\user::IsSignedin()): ?>
    window.cuid = '<?php echo sha1(\core\db\models\user::GetInstance()->user_id); ?>';
    $(".comment[data-commenter='"+window.cuid+"']:not(.init)")
        .find('.vote')
            .attr({'data-toggle': 'tooltip', 'title': 'You cannot vote your own stuff.'})
            .css("cursor", "default")
            .addClass("disabled init")
            .tooltip();
    $('.comments-container [data-toggle="tooltip"]:not(.init)').addClass('init').attr("data-placement", 'top').tooltip();
    
    $(".vote.vote-up:not(.disabled):not(.vote-event), .vote.vote-down:not(.disabled):not(.vote-event)").click(function(){
        if($(this).parents(".comment").attr("data-commenter") === window.cuid || ($(this).hasClass("disabled") && !$(this).hasClass("voted"))) { return; }
        var _data = {
            nid: <?php echo json_encode($this->note_id) ?>,
            cid: $(this).parents(".comment").attr('data-id')
        };
        var $_thisp = $(this).parents(".comment").find(".vote");
        var voted = ( $(this).parents(".comment").find(".vote.voted").length > 0 );
        if(voted)
            $.extend(_data, {voteup: -1});
        else
            $.extend(_data, {voteup: $(this).hasClass("vote-up") ? 1 : 0});
        $_thisp.addClass("disabled");
        $.ajax({
            global: false,
            url: "/comment/vote?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array($this->note_id))?>",
            type: "POST",
            data: _data,
            dataType: "json",
            success: function(data) {
                if(typeof(data.vote_up) === "undefined" || typeof(data.vote_down) === "undefined") {
                    window.open_errorModal("Sorry, something went wrong... The page will be reloaded!!");
                    setTimeout(function(){window.location.reload();}, 2000);
                }
                if(data.vote_up === null)
                    data.vote_up = 0;
                if(data.vote_down === null)
                    data.vote_down = 0;
                if(data.vote_up === 0) 
                    data.vote_up = "";
                if(data.vote_down === 0) 
                    data.vote_down = "";
                $_thisp.find(".vote-up-val").text(data.vote_up);
                $_thisp.find(".vote-down-val").text(data.vote_down);
                $_thisp.data("voted", !voted);
                switch(_data.voteup) {
                    case -1:
                        $_thisp.removeClass("disabled voted");
                        break;
                    case 0:
                        $_thisp.find(".vote-down-val").parents(".vote").addClass('voted');
                        break;
                    case 1:
                        $_thisp.find(".vote-up-val").parents(".vote").addClass('voted');
                        break;
                }
            }
        }).fail(function(xhr){
            setTimeout(function() { window.open_errorModal(xhr.responseText, -1, true); }, 500);
        }).always(function(){
            $_thisp.each(function(){
                if(!$(this).hasClass("voted"))
                    $(this).removeClass("disabled");
            });
        });
    }).addClass("vote-event");
    $(".delete-comment:not(.com-init), .report-comment:not(.com-init)").click(function(){
        if($(this).parents(".comment").hasClass("deleting")) return;
        $(this).parents(".comment").addClass('deleting').css("cursor", "progress");
        $.ajax({
            global: false,
            url: "/comment/mark/op/"+$(this).attr("op")+"?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array($this->note_id))?>",
            type: "POST",
            data: {
                nid: <?php echo json_encode($this->note_id); ?>,
                cid: $(this).parents('.comment').attr('data-id')
            },
            dataType: "json",
            success: function(data) {
                if(typeof(data.result) === "undefined")
                    data.result = 0;
                if(data.result) {
                    $(".comment.deleting").slideUp('slow', function(){$(this).remove();});
                    var cc = $(".total-comment-no .no").text();
                    $(".total-comment-no .no").html(parseInt(cc) - 1);
                    if(cc > 2)
                        $(".total-comment-no label").text("comments");
                    else
                        $(".total-comment-no label").text("comment");
                } else {
                    window.open_errorModal("Something went wrong, please try again.");
                }
            }
        }).fail(function(xhr){
            setTimeout(function() { window.open_errorModal(xhr.responseText, -1, true); }, 500);
        }).always(function(){
            setTimeout(function(){$(".comment.deleting").removeClass("deleting").css("cursor", "default");}, 1000);
        });
    }).addClass("com-init");
    $(".edit-comment:not(.com-init)").click(function(){
        if(typeof($(this).data("editing")) !== "undefined") return;
        $(this).data("editing", 1);
        var $t = $(this);
        var $p = $(this).parents(".comment");
        var $origin = $p.find(".comment-data").clone().wrap("<p/>").parent().html();
        var data = {
            url: "/comment/edit?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array($this->note_id))?>",
            data: {cid: $p.attr("data-id")},
            always: function() {$p.find(".cancel-edit").remove();$t.fadeIn();$t.removeData("editing");},
            success: function(data) {
                $p.replaceWith(data);
                window.init_comments();
            }
        };
        var c = $p.find(".comment-data").text();
        $t.fadeOut('fast', function() {
            $("<small class='cancel-edit'><a href='#'>Cancel</a></small>").click(function(e){
                e.preventDefault();
                $p.find("textarea[name='comment']").replaceWith($origin);
                data.always();
            }).appendTo($t.parent());
        });
        $p.find(".comment-data").replaceWith($("textarea[name='comment']").clone());
        window.init_comment_txtbox(
            $p.find("textarea[name='comment']").val(c).css({"max-width": "95%", "margin-left":"20px"}),
            data
        );
    }).addClass("com-init");
<?php endif; ?>
<?php if($this->count_of_comments): ?>
    $(".load-more-comment:not(.head-comments-inited)").click(function(){
        if(typeof($(this).data("init-text")) === "undefined")
            $(this).data("init-text", $(this).html());
        if(typeof($(this).data("nextp")) === "undefined")
            $(this).data("nextp", 2);
        if(typeof($(this).data("newload")) !== "undefined") $(".comments").addClass("deleting");
        if(typeof($(this).data("loading")) !== "undefined") return;
        $(this).data("loading", 1);
        $(this).text("Loading ....");
        var $this = $(this);
        var is_more = false;
        $.ajax({
            global: false,
            url: "/fetch/comment?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array($this->note_id)) ?>",
            type: "POST",
            dataType: "json",
            data: {
                nid: <?php echo json_encode($this->note_id); ?>,
                p: $(this).data("nextp"),
                type: $(".comments-head .active a").attr("op")
            },
            success: function(data) {
                $(".comments").removeClass("deleting");
                if(typeof($this.data("newload")) !== "undefined") {
                    $(".comments").html(data.comments);
                    $this.removeData("newload");
                } else {
                    $(".comments").append(data.comments);
                }
                $this.data("nextp", data.nextp);
                is_more = data.is_more;
                window.init_comments();
            }
        }).fail(function(xhr){
            setTimeout(function() { window.open_errorModal(xhr.responseText, -1, true); }, 500);
        }).always(function(){
            if(is_more)
                $this.html($this.data("init-text"));
            else
                $this.parents(".load-more-comment-container").hide();
            $this.removeData("loading").show();
        });
    }).addClass("head-comments-inited");
    $(".top-comments:not(.head-comments-inited), .all-comments:not(.head-comments-inited)").click(function(){
        if(typeof($(this).parent().data("loading")) !== "undefined" || typeof($(".load-more-comment").data("loading")) !== "undefined") return;
        $(".comments a").off();
        $(".comment a[href=#]").click(function(){return false;});
        $(this).parent().addClass("active").siblings().removeClass("active");
        $(".load-more-comment").data({"nextp": 1, "newload": 1}).hide().click();
        $(this).parent().removeData("loading");
    }).addClass("head-comments-inited");
<?php endif; ?>
};
$(document).ready(function(){window.init_comments();});
</script>
<?php
    }
}