<?php
namespace modules\defaultModule\views\layout;
/**
 * renders general layout header
 *
 * @author dariush
 */
class genLayoutHeader extends \zinux\kernel\layout\baseLayout
{
    /**
     * @var \zinux\kernel\layout\baseLayout
     */
    protected $layout;
            
    public function __construct(\zinux\kernel\layout\baseLayout $layout)
    {
        $this->layout = $layout;
    }
    function render_header() {
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- twitter bootstrap -->
        <link rel="stylesheet" href="/access/css/bootstrap.min.css">
        <link rel="stylesheet" href="/access/css/bootstrap-theme.min.css">
        <!-- jQuery -->
        <script type="text/javascript" src="/access/js/jquery-1.11.1.min.js"></script>
        <!-- font awsome -->
        <link  rel="stylesheet" href="/access/css/font-awesome.min.css">
        <link rel="shortcut icon" href="/favicon.ico">
        <?php $this->layout->RenderTitle();?>
        <?php $this->layout->RenderImports(); ?>

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <style type="text/css">
            html,body{margin:0px;padding:0px;height:100%;}/*Custom page header*/.header{border-bottom:1px solid #e5e5e5;}/*Custom page footer*/.footer{padding-top:1%;color:#777;border-top:1px solid #e5e5e5;clear:both;}/*Responsive : Portrait tablets and up*/@media screen and(min-width:768px){/*Remove the padding we set earlier*/.header,.tor,.footer{padding-left:0;padding-right:0;}/*Spaceout the masthead*/.header{margin-bottom:1%;}}/*iframe gen. attribs */iframe{width:100%;border-style:none;border-color:transparent;border-width:0px;}
        </style>
    </head>
    <body>
        <noscript>
            <?php \core\ui\html\alert::Tout("Your browser's javascript <b>is not</b> enabled. To site be able to work you have to enable your javascript.", \core\ui\html\alert::ALERT_DANGER); ?>
        </noscript>
        <div class="header">
            <?php if(($user_logged = \core\db\models\user::IsSignedin())): ?>
            <style type="text/css">
                .header .nav .badge {background-color:#4488cc;}
                .notifications.dropdown-menu{padding: 0;width: 400px;}
                .header .nav .notification {}
                .header .nav .notification-label {display: block!important}
                .header .nav .notification-core {padding: 10px;}
                .header .nav .notification-core-text {margin-left: 10px;word-wrap: break-word!important;white-space: normal}
                .header .nav .notifs-ops {padding:3px 10px 10px 10px;border-bottom: 1px solid #e9e9e9;}
                .header .nav .notifications {max-height: 400px;overflow-x: hidden;overflow-y: auto}
                .header .nav .notifications.dropdown-menu li.no-notification {padding:10px;font-variant: small-caps}
                .header .nav .notifications.dropdown-menu li.no-notification > a:hover,
                .header .nav .notifications.dropdown-menu li.no-notification > a:focus,
                .header .nav .notifications.dropdown-submenu:hover > a {background-image: none; background-color:transparent}
            </style>
            <script type='text/javascript'>
                $(document).ready(function(){
                    function init_notifications() {
                        if($(".notification").length === 0) {
                            $(".notifications").append("<li class='no-notification notification text-center'><a style='font-weight: bold;color:#9e9e9e'>No notification!</a></li>");
                            $(".notification-badge.badge").remove();
                            $(".notification-clear-all").remove();
                        } else {
                            $(".notification:not(.init)").click(function(e){
                                e.stopPropagation();
                            }).addClass("init");
                        }
                    }
                    init_notifications();
                    $(".notification-clear-all").click(function(e){
                        e.stopPropagation();
                        e.preventDefault();
                        function rename_attrib($they, from, to) {
                            $they.each(function(){
                                $(this).attr(to, $(this).attr(from));
                                $(this).removeAttr(from);
                            });
                        }
                        $.ajax({
                            global: false,
                            beforeSend:  function() {
                                $(".notification-clear-all").hide().parent().append("<span class='fa fa-spin fa-spinner notification-clear-spin'><span>");
                               rename_attrib($(".notification").addClass("disabled").find("a"), "href", "data-href");
                            },
                            complete:  function() {
                                setTimeout(function(){ $(".notification-clear-spin").remove(); $(".notification-clear-all").show(); }, 750);
                                rename_attrib($(".notification").removeClass("disabled").find("a"), "data-href", "href");
                            },
                            url: "/notifications/clear?<?php echo \zinux\kernel\security\security::__get_uri_hash_string(array(0, 10, \core\db\models\user::GetInstance()->user_id))?>",
                            data: {o: 0, l: 10},
                            success: function(data){ 
                                $(".notification").each(function(){
                                    $(this).slideUp(function(){$(this).remove();init_notifications();});
                                });
                            },
                            error: function(data) {
                                window.open_errorModal("Couldn't complete the operation. please try later.");
                            }
                        });
                    });
                });
            </script>
            <?php endif; ?>
            <ul class="nav nav-pills pull-right" style="padding-top: 0.25%;">
                <?php if($user_logged): ?>
                <li class='hidden-lg hidden-md'><a href='#'><span class='glyphicon glyphicon-flash'></span> Feeds</a></li>
                <li class="dropdown">
                    <?php
                        $n = new \core\db\models\notification;
                        $notif_pulls = $n->pull(\core\db\models\user::GetInstance()->user_id);
                    ?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span  data-toggle='tooltip' title='Notification' data-placement='bottom'>
                            <?php echo count($notif_pulls) ? "<span class='notification-badge badge'>".count($notif_pulls)."</span>" : "" ?> <span class="fa fa-bell" ></span><b class="caret"></b>
                        </span>
                    </a>
                    <ul class="dropdown-menu pull-right notifications">
                        <li class="notifs-ops">
                            <div class="pull-left">
                                <strong>Notifications</strong>
                            </div>
                            <div class="pull-right">
                                <a href='#' class='notification-clear-all'>Clear these</a>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <?php
                            foreach($notif_pulls as $notif) :
                        ?>
                        <li class="notification">
                            <a href='<?php echo array_shift(explode("#", substr($notif->notification_link, 2)))?>?ref=notif' class="notification-core">
                                <small class="text-muted block notification-label" style="font-variant: small-caps"><?php echo ucwords($notif->notification_title)?> &cross; <span class='badge'><?php echo $notif->count?></span></small>
                                <div class="notification-core-text"><?php echo $notif->notification_message?></div>
                            </a>
                        </li>
                        <?php
                            endforeach;
                        ?>
                    </ul>
                </li>
                <li><a href='/messages' data-toggle='tooltip' title='Inbox' data-placement='bottom'>
                <?php 
                    $inbox_count = \core\db\models\conversation::countAll(\core\db\models\user::GetInstance()->user_id, \core\db\models\abstractModel::FLAG_SET, \core\db\models\abstractModel::FLAG_UNSET);
                    if($inbox_count)
                        echo "<span class='badge'>$inbox_count</span>";
                    unset($inbox_count);
                ?></span> <span class='glyphicon glyphicon-inbox'></span></a></li>
                <li class="dropdown">
                    <?php list($avatar_uri , $def_avatar) = \core\ui\html\avatar::get_avatar_link(\core\db\models\user::GetInstance()->user_id); ?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src='<?php echo $avatar_uri ?>' height="20" width="20" class='imageblock img-rounded' onerror="this.src='<?php echo $def_avatar ?>'"/> Account <b class="caret"></b></a>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="/profile">Profile</a></li>
                        <li><a href="#">Account Settings</a></li>
                        <li class="divider"></li>
                        <li><a href="/signout">Signout</a></li>
                    </ul>
                </li>
                <?php else: ?>
                    <li><a href="/signin">Signin</a></li>
                    <li><a href="/signup">Signup</a></li>
                <?php endif; ?>
            </ul>
            <a href="/" style="font-size: x-large;" class="navbar-brand text-muted">Toratan</a>
            <div class="clearfix"></div>
        </div>
        <script type="text/javascript">
            (function(){
                // if we are in a frame?
                if(window.top !== window.self)
                    // remove the header
                    $(".header").remove();
            })(jQuery);
        </script>
        <div class="clearfix" style="margin-top:10px;"></div>
        <?php new \core\ui\html\dialogs(); ?>
<?php
    }
    public function render_footer() {
?>
        <style type="text/css">
            div#global-footer {border-top: 1px solid #e6e6e6; margin-top: 15px; padding:15px 10px 0 10px;}
            div#global-footer .separator {color:#e6e6e6}
            div#global-footer .separator:after {content: '|';}
        </style>
        <div id="global-footer">
            <ul class="list-inline pull-left" id="footer-left" style="color: #a6a6a6">
                <li>&COPY; <?php echo  date('Y') ?> Toratan</li>
            </ul>
            <ul class="list-inline pull-right" id="footer-statements">
                <li><a href="<?php echo \zinux\kernel\application\config::GetConfig("toratan.statement.about") ?>">About</a></li>
                <li class="separator"></li>
                <li><a href="<?php echo \zinux\kernel\application\config::GetConfig("toratan.help") ?>">Help</a></li>
                <li class="separator"></li>
                <li><a href="<?php echo \zinux\kernel\application\config::GetConfig("toratan.statement.terms") ?>">Terms</a></li>
                <li class="separator"></li>
                <li><a href="<?php echo \zinux\kernel\application\config::GetConfig("toratan.statement.privacy") ?>">Privacy</a></li>
                <li class="separator"></li>
                <li><a href="<?php echo \zinux\kernel\application\config::GetConfig("toratan.statement.cookies") ?>">Cookies</a></li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <script type='text/javascript'>
                $(document).ready(function(){
                    $('[data-toggle="tooltip"]:not([data-placement])').attr("data-placement", 'top');
                    $('[data-toggle="tooltip"]').tooltip();
                });
        </script>
        <script type="text/javascript" src="/access/js/bootstrap.min.js"></script>
    </body>
</html>
<?php
    }
}
