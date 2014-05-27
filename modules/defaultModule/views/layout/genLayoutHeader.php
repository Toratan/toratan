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
        <script type="text/javascript" src="/access/js/jquery-1.10.2.min.js"></script>
        <link rel="shortcut icon" href="/favicon.ico">
        <?php $this->layout->RenderTitle();?>
        <?php $this->layout->RenderImports(); ?>

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <style>
            html,body{margin:0px;padding:0px;height:100%;}/*Custom page header*/.header{border-bottom:1px solid #e5e5e5;}/*Custom page footer*/.footer{padding-top:1%;color:#777;border-top:1px solid #e5e5e5;clear:both;}/*Responsive : Portrait tablets and up*/@media screen and(min-width:768px){/*Remove the padding we set earlier*/.header,.tor,.footer{padding-left:0;padding-right:0;}/*Spaceout the masthead*/.header{margin-bottom:1%;}}/*iframe gen. attribs */iframe{width:100%;border-style:none;border-color:transparent;border-width:0px;}
        </style>
    </head>
    <body>
        <noscript>
            <?php \core\ui\html\alert::Cout("!!Your browser's javascript IS NOT enabled!!", \core\ui\html\alert::ALERT_DANGER); ?>
        </noscript>
        <div class="header">
            <ul class="nav nav-pills pull-right" style="padding-top: 0.25%;">
                <?php if(($user_logged = \core\db\models\user::IsSignedin())): ?>
                <li class='hidden-lg hidden-md'><a href='#'><span class='glyphicon glyphicon-flash'></span> Feeds</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge">2</span> Notifications<b class="caret"></b></a>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="#">Some stuff</a></li>
                        <li><a href="#">Some other stuff</a></li>
                    </ul>
                </li>
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
            <a href="/" style="font-size: x-large;" class="navbar-brand text-muted" >Toratan</a>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix" style="margin-top:10px;"></div>
<?php
    }
    public function render_footer() {
?>
        <script type="text/javascript" src="/access/js/bootstrap.min.js"></script>
    </body>
</html>
<?php
    }
}
