<?php
namespace modules\opsModule\models;
    
/**
* The modules\opsModule\models\tagit
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class tagit
{
    public function __renderJS(){
?>
<script type='text/javascript'>
    function close_tag_sug() {
        $('.tag-sug-container')
            .data('closed', 1)
            .slideUp('fast');
    }
    function submit_tag(tag) {
        $(".myTags").tagit("createTag", tag);
        new_tag_focus();
    }
    function new_tag_focus() {
        $(".myTags")
            .data('uiTagit')
                .tagInput
                    .focus();
    }
    function tagit(){
        close_tag_sug();
        $("ul.tagit").remove();
        window.open_savecloseModal($("#tagit-container").html(), function(){
            $("#tagit-container .myTags").val($(".modal-body .myTags").val());
            if(typeof(document.tagit_callback) !== "undefined" && typeof(document.tagit_callback) === "function")
                document.tagit_callback($(".modal-body .myTags").val());
        });
        setTimeout(function(){
            // it the window is opened by user we are recording the changes
            $(window).data("tagit_change_record", true);
            // focus on new tag input
            new_tag_focus();
            // to disable unblur submit tag event
            $(".myTags")
                .data('uiTagit')
                    .tagInput
                        .unbind("blur");
        }, 600);
    }
    function is_tags_changed() {
        var ic = $(".myTags").data('is_changed');
        if(typeof(ic) !== "undefined" && ic)
            return true;
        return false;
    }
</script>
<script src="/access/js/jquery-ui-1.11.0.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/access/js/tag-it/js/tag-it.min.js" type="text/javascript" charset="utf-8"></script>
<?php
    }
    public function __renderCSS(){
?>
<style type="text/css">
    .tag-sug-container {
        position: fixed;
        margin-top: -5px;
        width: calc(100% - 41px);
        height: auto;
        border: 1px solid #000;
        background-color: #FFF;
        display: none;
        padding:10px;
    }
    .tagit-choice {
        -moz-user-select: none; 
        -khtml-user-select: none; 
        -webkit-user-select: none; 
        -o-user-select: none; 
    }
    /* remove unwanted jQuery-UI SHIT!! */
    .ui-helper-hidden-accessible, ui-autocomplete{ visibility: collapse!important;display: none!important;}
</style>
<link href="/access/js/tag-it/css/jquery.tagit.css" rel="stylesheet" type="text/css">
<link href="/access/js/tag-it/css/tagit.ui-zendesk.css" rel="stylesheet" type="text/css">
<?php
    }
    public function __renderHTML($tags, $html_input_name = "tagit"){
?>
    <div id="tagit-container" style="display: none">
        <label>Tag this note (Separate with SPACE):</label>
        <div class='pull-right tag-fetching-wait' style='display: none'><img src='/access/img/ajax-loader.gif' alt="Searching..." title='Fetching similar tags....'/></div>
        <div class="text-danger tag-error-overflow" style='font-weight: bold;margin-bottom: 10px;display: none'>
            <span class='glyphicon glyphicon-warning-sign'></span> Tag's length cannot be more than 50 charactor!
        </div>
        <div class="text-danger tag-error-ajax" style='font-weight: bold;padding-bottom: 10px;display: none'></div>
        <input name='<?php echo $html_input_name ?>' class='myTags' type="hidden" value='<?php echo $tags ?>'/>
        <script type="text/javascript">
            function get_tags_string() {
                return $(".myTags").tagit("assignedTags");
            }
            $(document).ready(function() {
                $(".myTags").tagit({
                    placeholderText: "Add a tag....",
                    beforeTagRemoved: function(event, ui) {
                        if(String(ui.tagLabel).trim().length === 0) return;
                        $(ui.tag).remove();
                    },
                    afterTagRemoved: function(event, ui) {
                        $(".myTags").data("is_changed", true);
                    },
                    beforeTagAdded: function(event, ui) {
                        var retval = ui.tagLabel.length <= 50;
                        if(!retval)
                            $(".tag-error-overflow").fadeIn();
                        else
                            $(".tag-error-overflow").fadeOut();
                        return retval;
                    },
                    afterTagAdded: function(event, ui) {
                        close_tag_sug();
                        // this one is for escaping the is_change flag from initial tag-add op
                        if(typeof($(window).data("tagit_change_record")) !== "undefined")
                            $(".myTags").data("is_changed", true);
                    }
                }).data('uiTagit')
                        .tagInput
                            .keyup(function(e){
                                clearTimeout($(this).data("autocomplete_delay"));
                                close_tag_sug();
                                if($(this).data("autocomplete_prev_txt") === $(this).val() || $(this).val().trim().length === 0) return;
                                clearTimeout($(this).data("autocomplete_delay"));
                                var val = $(this).val();
                                var $this = $(this);
                                $(this).data("autocomplete_delay", setTimeout(function(){
                                    $this.data("autocomplete_prev_txt", val);
                                    $(".tag-fetching-wait").show();
                                    $(".tag-error-ajax").html("").slideUp();
                                    $.ajax({
                                        url: "/ops/fetch/tags?<?php echo \zinux\kernel\security\security::__get_uri_hash_string() ?>&term="+val,
                                        type: "POST",
                                        global: false,
                                        success: function(data) {
                                            $(".tag-sug-container")
                                                .html(data)
                                                .slideDown();
                                        }
                                    }).complete(function(){
                                        $(".tag-fetching-wait").fadeOut();
                                    }).fail(function(xhr) {
                                        var info = xhr.responseText;
                                        var $body = $('<div>').append($(info)).find("#error-layout").wrap("<div>");
                                        if($body.length !== 0) {
                                            $body.find(".container").removeClass("container").css("margin", "-10px").html();
                                            info = $body.html();
                                        }
                                        $(".tag-error-ajax").html(info).fadeIn();
                                    });
                                }, 500));
                            })
                            // to disable unblur submit tag event
                            .unbind("blur");
            });
        </script>
        <div class="tag-sug-container">
            <a href='#' style='position: absolute;bottom: 0px;right: 10px;' class="close-tag-sug block" onclick="close_tag_sug(); return false;">Close</a>
        </div>
    </div>
<?php
    }
}