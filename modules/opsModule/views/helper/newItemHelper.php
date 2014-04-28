<?php
namespace modules\opsModule\views\helper;

class newItemHelper {
    static function plotJS(\zinux\kernel\view\baseView $view, $target_type) {
        switch ($target_type) {
            case "folder":
            case "link":
                break;
            default: throw new \zinux\kernel\exceptions\invalideArgumentException("Undefined `$target_type`");
        }
?>
    <script type="text/javascript">
            $(document).ready(function(){
                setTimeout(function() { $('input[name="<?php echo $target_type ?>_title"]').focus(); }, 500);
                $("#new-<?php echo $target_type ?>-ui form" ).submit(function(e) {
                    e.preventDefault();
                    $.ajax({
                        type: $(this).attr("method"),
                        url: $(this).attr("action"),
                        data: $(this).serialize()+"&ajax=1&suppress_redirection=1&"+$(this).attr("action").split("?")[1],
                        success: function(<?php echo !isset($view->values["{$target_type}_title"])?"data":"" ?>) {
    <?php if(isset($view->values["{$target_type}_title"])): ?>
                            window.apply_change(
                                window.APPLY_NAME_EDITTED,
                                $("input[name='<?php echo $target_type ?>_title']").val()
                            );
    <?php else: ?>
                            // unbind the success method to ignore the general result output
                            $(window).unbind('ajaxSuccess');
                            $(data).hide().appendTo("#explorer-table table.table").fadeIn(1500);
                            window.init_times();
                            window.reset_ajax_placeholder();
                            window.sort_table();
                            // set a timeout(50ms) for re-binding the ajax success method
                            setTimeout(function() { $(window).ajaxSuccess(window.ajax_success); }, 50);
    <?php endif; ?>
                        }
                    });
                });
            });
    </script>
<?php
    }
}