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
            <style type="text/css">
                .header .nav .badge {background-color:#4488cc;}
                .notificationdropdown-menu{padding: 0;max-height: 100px;overflow-y: auto}
                .header .nav .notification{padding: 10px;width: 400px;}
                .header .nav .notification-label {display: block!important}
                .header .nav .notification-core-text {margin-left: 40px; word-break: break-all;}
            </style>
            <ul class="nav nav-pills pull-right" style="padding-top: 0.25%;">
                <?php if(($user_logged = \core\db\models\user::IsSignedin())): ?>
                <li class='hidden-lg hidden-md'><a href='#'><span class='glyphicon glyphicon-flash'></span> Feeds</a></li>
                <li class="dropdown">
                    <?php
                        $n = new \core\db\models\notification;
                        $notif_pulls = $n->pull();
                    ?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo count($notif_pulls) ? "<span class='badge'>".count($notif_pulls)."</span>" : "" ?> Notifications<b class="caret"></b></a>
                    <ul class="dropdown-menu pull-right notification-dropdown-menu">
                        <?php
                            foreach($notif_pulls as $notif) :
                        ?>
                        <li class="notification">
                            <a href='<?php echo substr($notif->notification_link, 2)?>'>
                                <small class="text-muted block notification-label"><?php echo ucwords($notif->notification_title)?> &cross; <span class='badge'><?php echo $notif->count?></span></small>
                                <span class="notification-core-text"><?php echo $notif->notification_message?></span>
                            </a>
                        </li>
                        <?php
                            endforeach;
                        ?>
                    </ul>
                </li>
                <li><a href='/messages'>
                <?php 
                    $inbox_count = \core\db\models\conversation::countAll(\core\db\models\user::GetInstance()->user_id, \core\db\models\abstractModel::FLAG_SET, \core\db\models\abstractModel::FLAG_UNSET);
                    if($inbox_count)
                        echo "<span class='badge'>$inbox_count</span>";
                    unset($inbox_count);
                ?></span> <span class='glyphicon glyphicon-inbox'></span> Inbox</a></li>
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
