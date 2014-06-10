<?php
namespace modules\frameModule\models;
class bc {

    public static function plot(\zinux\kernel\view\baseView $view, \modules\frameModule\models\directoryTree $dt) {
            if(!isset($dt)) throw new \zinux\kernel\exceptions\invalidArgumentException("un-initialized \$dt instance.");
            /**
             * checks if should the passed item involed in breadcrumb list with given tree type
             */
            $should_apply2breadcrumb = function(\core\db\models\item $item, $tree_type) {
                static $apply = false;
                if($apply) return true;
                switch ($tree_type) {
                    case \modules\frameModule\models\directoryTree::REGULAR: return true;
                    case \modules\frameModule\models\directoryTree::ARCHIVE: return ($apply = $item->is_archive);
                    case \modules\frameModule\models\directoryTree::SHARED: return ($apply = $item->is_public);
                    case \modules\frameModule\models\directoryTree::TRASH: return ($apply = $item->is_trash);
                    default: throw new \zinux\kernel\exceptions\invalidArgumentException("invalid tree type `{$tree_type}`");
                }
            }
        ?>
        <ol id="bc-template" class="breadcrumb">
        <?php
            $i = 1;
            # loading routes
            foreach ($view->route as $folder) :  $is_active = ($i++==count($view->route)); if(!$folder->folder_id || !$should_apply2breadcrumb($folder, $dt->tree_type)) continue; ?>
                <li <?php echo $is_active?" class='active'":""?>>
                    <?php 
                        if(!$is_active)
                            echo "<a link-type='folder' status='{$dt->getStatusBinary($folder)}' class='table-nav-link' href='{$dt->getNavigationLink($folder)}'>";
                        echo "<span hash-link='{$dt->getNavigationLink($folder)}'>",$folder->folder_title, "</span>";
                        if(!$is_active)
                            echo "</a>";
                        else $folder;
                    ?>
                </li>
        <?php endforeach; ?>
        </ol>
        <script>
            (function(){
        <?php
            $type = $view->request->action->name;
            if($dt->tree_type==\modules\frameModule\models\directoryTree::REGULAR) $type = "root";
        ?>
                if(typeof(window.top.active_nav) !== "undefined")
                    window.top.active_nav('<?php echo  $type ?>');
                if(typeof(window.top.getActiveNavHtml) !== "undefined")
                    $("ol#bc-template.breadcrumb")
                            .prepend("<li>"+$(window.top.getActiveNavHtml()).wrapInner("<span hash-link='"+window.top.getActiveTypeLink()+"'></span>").wrap("<p>").parent().html()+"</li>")
                            .find("li:last")
                                .addClass("active")
                                .html($("#explorer-template ol.breadcrumb li:last a").html());
            })(jQuery);
        </script>
<?php
        unset($should_apply2breadcrumb);
    }
}
?>