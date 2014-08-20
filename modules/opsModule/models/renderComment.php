<?php
namespace modules\opsModule\models;
/**
* The modules\opsModule\models\renderComment
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class renderComment
{
    protected $comments;
    protected $count_of_comments;
    protected $is_more;
    /**
     * Construct a comment renderer
     * @param array $comments array of comments
     * @param integer $count_of_comments (default: NULL) The count of total comments, if NULL passed, it will set automatically to `count($comments)`.
     */
    public function __construct(array $comments, $count_of_comments = 0) {
        $this->comments = $comments;
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
<style type="text/css">.comments-container{padding:20px 40px}.comments-container>:not(.clearfix){margin-bottom:10px}.comments-container .avatar img{-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px}.comments-container .total-comment-no{font-weight:700;font-size:large}.comments-container .total-comment-no label{font-weight:700;font-size:medium}.comments-container .prev-comment-area .comments-head{border-bottom:2px solid #EEE;margin-bottom:20px}.comments-container .prev-comment-area .comments-head li{width:70px;padding:5px}.comments-container .prev-comment-area .comments-head li a{text-decoration:none;font-weight:700;color:#7c7c7c}.comments-container .prev-comment-area .comments-head li.active a{font-weight:bolder;color:#000}.comments-container .prev-comment-area .comments-head li a{display:block}.comments-container .prev-comment-area .comments-head li:not(.active) a:hover{color:#5a5a5a}.comments-container .prev-comment-area .comments-head li:not(.active):hover{border-bottom:2px solid #fc4}.comments-container .prev-comment-area .comments-head li.active{border-bottom:2px solid #08c}.comments-container .prev-comment-area .comments-head li .careta{margin-top:10px;margin-left:3px}.comments-container .prev-comment-area .comments-head li:not(.active) .caret{visibility:collapse}.comments-container .user-comment-erea .form-control{-webkit-border-radius:0;-moz-border-radius:0;border-radius:0}.comments-container .prev-comment-area .comment{margin-bottom:30px}.comments-container .prev-comment-area .comment .comment-body{padding-left:30px}.comments-container .prev-comment-area .comment .comment-body .commenter-detail .commenter-link{font-weight:bolder;display:inline}.comments-container .prev-comment-area .comment .comment-body .commenter-detail .comment-date{display:inline;color:#AAA;font-weight:700;font-size:small}.comments-container .prev-comment-area .comment .comment-body .commenter-detail .comment-date::before{content:" . "}.comments-container .prev-comment-area .comment .comment-body .comment-data{margin:10px;overflow:auto}.comments-container .prev-comment-area .comment .comment-body .comment-footer{margin:0 10px}.comments-container .prev-comment-area .comment .comment-body .comment-footer .vote{text-decoration:none}.comments-container .prev-comment-area .comment .comment-body .comment-footer .vote:hover{font-weight:700}.comments-container .prev-comment-area .comment .comment-body .comment-footer>*{display:inline;padding:3px}.comments-container .prev-comment-area .comment .comment-body .comment-footer .divider{padding-top:-10px}.comments-container .prev-comment-area .comment .comment-body .comment-footer .divider::after{content:"."}.comments-container .prev-comment-area .comment .comment-body .comment-footer .actions *{padding:0;margin:0}.comments-container .prev-comment-area .comment .comment-body .comment-footer .actions a{color:#08c;padding:2px}.comments-container .prev-comment-area .load-more-comment{border:1px solid #ddd;height:45px;padding-top:10px}.comments-container .prev-comment-area .comment.my-comment{border-left:2px solid #08c;padding-left:10px;margin-left:-10px}@media screen and (min-width:0) and (max-width:399px){.comments-container .user-comment-erea .comment-signin-container .burden{display:none}}@media screen and (min-width:0) and (max-width:700px){.comments-container .prev-comment-area .comment .comment-body{padding-left:10px}}</style>
<?php
    }
    public function __render_new_comment() {
?>
<?php if($this->count_of_comments) : ?>
<div  class="total-comment-no"><?php echo $this->count_of_comments; ?> <label><?php echo \ActiveRecord\Utils::pluralize_if($this->count_of_comments, "comment")?></label></div>
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
            <script src="/access/js/autosize/jquery.autosize.min.js" type="text/javascript"></script>
            <script type="text/javascript">
                (function(){
                    $("textarea[name='comment']").autosize();
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
    <?php if(true || $this->count_of_comments) : ?>
    <ul class="list-inline comments-head">
        <li class="active"><a href="#">Best  <span class='caret'></span></a></li>
        <li><a href="#">All <span class='caret'></span></a></li>
    </ul>
    <?php endif; ?>
    <div class="comments">
<?php
    }
    public function __render_prev_comments() {
        $cuid = @\core\db\models\user::GetInstance()->user_id;
        $comments = $this->comments;
?>
<?php foreach($comments as $comment): ?>
<?php list($avatar_uri , $def_avatar) = \core\ui\html\avatar::get_avatar_link($comment->user_id); ?>
<div class="comment <?php echo $comment->user_id == $cuid ? "my-comment" : "" ?>">
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
                    <div class="comment-date"><time datetime="<?php echo $comment->created_at ?>" class="timeago"><?php echo $comment->created_at ?></time></div>
                </div>
                <div class="col-xs-12 comment-data"><?php echo $comment->comment ?></div>
                <div class="col-xs-12 comment-footer">
                    <a href="#" class="vote vote-up text-success"><span class="vote-up-val"><?php echo $comment->vote_up ? $comment->vote_up : "" ?></span> <span class="glyphicon glyphicon-chevron-up"></span></a>
                    <div class="divider"></div>
                    <a href="#" class="vote vote-down text-danger"><span class="vote-down-val"><?php echo $comment->vote_down ? $comment->vote_down : "" ?></span> <span class="glyphicon glyphicon-chevron-down"></span></a>
                    <div class="actions pull-right">
                        <ul class="list-inline">
                            <li><a href="#">Edit</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Delete</a></li>
                        </ul>
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
        <?php if($this->is_more) : ?>
        <div class='load-more-comment text-center'>
            <a href="#">Load more comments.</a>
        </div>
        <?php endif; ?>
    </div>
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
$(document).ready(function(){
    $("time.timeago").each(function(){
        var date = moment($(this).attr("datetime"));
        $(this)
            .html(date.fromNow("lll") + " ago")
            .attr('title', date.format("lll"))
            .attr('data-toggle', 'tooltip')
            .css("cursor", "pointer");
    });
});
</script>
<?php
    }
}