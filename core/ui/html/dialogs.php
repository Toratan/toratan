<?php
namespace core\ui\html;
/**
 * Description of dialogs
 *
 * @author dariush
 */
class dialogs
{
    function __construct ()
    {
?><script>
    (function() {
        window.default_modal_button = new (function() {
            this.get = function() { return {
                            html: "Close",
                            attrib: [
                                {key: "name", value: "close"},
                                {key: "data-dismiss", value: "modal"}
                            ],
                            cssClass: ["btn-primary"],
                            callback: null
                        }; 
            };
        });
        /**
         * Opens a modal
         * @param string modal_tag the target modal tag
         * @param string data the data to put into modal body
         * @param integer close_timeout the timeout for closing modal
         * @param array __button and array of button informations to add to modal { default: close button }
         * @notice __button : each entity should have a format of following {html: 'html context string of button', attrib: ' array attributes of  button', cssClass: 'array of css classes', callback: 'call back function of button'
         * 
         */
        window.open_modal = function(modal_tag, data, title, close_timeout, __buttons) {
            if(typeof(title) === 'undefined') title = '<span class="glyphicon glyphicon-info-sign"></span> Dialog';
            if(typeof(close_timeout) === 'undefined') close_timeout = -1;
            if(typeof(__buttons) === 'undefined') {
                __buttons = [ default_modal_button.get() ];
            }
            $(modal_tag+" .modal-body").html(data);
            $(modal_tag+" .modal-title").html(title);
            $(modal_tag+" .modal-footer").html('');
            for(var index = 0; index < __buttons.length; index++) {
                var b = __buttons[index];
                if(typeof(b.html) === 'undefined') {
                    throw "No `html` property defined for button";
                }
                var btn_id = "modal-btn-"+(String(modal_tag).replace(/(\.|#)/ig, "", modal_tag))+"-"+index.toString();
                var t = '<button id="'+btn_id+'" type="button" class="btn ';
                for(var cssclass = 0; cssclass <  b.cssClass.length; cssclass++) {
                    t += " "+b.cssClass[cssclass];
                }
                t += '"'; // for classes
                for(var attribIndex = 0; attribIndex <  b.attrib.length, typeof(b.attrib[attribIndex]) !== "undefined"; attribIndex++) {
                   t += " " + b.attrib[attribIndex].key + "='" + b.attrib[attribIndex].value+"'";
               }
                t += (">"+b.html + '</button>');
                $(modal_tag+" .modal-footer").append(t).html();
                if(typeof(b.callback) !== 'undefined' && b.callback !== null) {
                    $(modal_tag+" .modal-footer button#"+btn_id).click(function() {
                        var callback = b.callback;
                        // we have to do this to prevent a UI bug
                        $(modal_tag).on('hidden.bs.modal', function () {
                            // unregister current handler
                            // we don't want to repeat this handler
                            // at next modal show/hidden!!
                            $(this).off('hidden.bs.modal');
                            // call the callback
                            callback();
                        }).modal('hide');
                    });
                }
            }
            $(modal_tag).modal('show');
            if(close_timeout > 0) {
                setTimeout(function(tag){
                    $(tag).modal('hide');
                }, close_timeout, modal_tag);
            }
        };
        /**
         * open an info modal  
         * @param string info the info to put into modal body
         * @param integer close_timeout the timeout for closing modal
         */
        window.open_infoModal = function(info, close_timeout) {
            open_modal("div#dialog-modal", info, '<span class="glyphicon glyphicon-comment"></span> Notice', close_timeout);
        };
        /**
         * open an error modal  
         * @param string info the info to put into modal body
         * @param integer close_timeout the timeout for closing modal
         */
        window.open_errorModal = function(info, close_timeout) {
            open_modal("div#dialog-modal", info, '<span class="glyphicon glyphicon-remove-sign"></span> Oops!', close_timeout);
        };
        /**
         * open a wait modal
         * @param integer close_modal instruct to close any wait modal
         * @param integer close_timeout the timeout for closing modal
         */
        window.open_waitModal = function(close_modal, close_timeout) {
            if(typeof(close_timeout) === 'undefined') close_timeout = -1;
            if(typeof(close_modal) === 'undefined') close_modal = false;
            var modal_tag = "div#wait-modal";
            if(close_modal) {
                $(modal_tag).modal('hide'); 
                return;
            }
            $(modal_tag).modal('show');
            if(close_timeout > 0) {
                setTimeout(function(modal_tag){
                    $(modal_tag).modal('hide');
                }, close_timeout, modal_tag);
            }
        };
        /**
         * open a YES/NO modal
         * @param string info the info to put into modal body
         * @param function yes_callback
         * @param function no_callback
         * @param boolean is_yes_primary check if `YES` is primary or `NO`
         * @param integer close_timeout the timeout for closing modal
         */
        window.open_yesnoModal = function(info, yes_callback, no_callback, is_yes_primary, close_timeout) {
            if(typeof(is_yes_primary) === "undefined") is_yes_primary = 1;
            var yes = default_modal_button.get();
            var no = default_modal_button.get();
            yes.callback = yes_callback;
            no.callback = no_callback;
            yes.html = "Yes";
            no.html = "No";
            yes.attrib[0].value = "yes";
            no.attrib[0].value = "no";
            if(is_yes_primary) no.cssClass[0] = "btn-default";
            else yes.cssClass[0] = "btn-default";
            open_modal("div#dialog-modal", info, '<span class="glyphicon glyphicon-question-sign"></span> Confirm', close_timeout, [no, yes]);
        };
        /**
         * open a SAVE/CLOSE modal
         * @param string info the info to put into modal body
         * @param function save_callback
         * @param string title The dialog's title(default: Dialog)
         * @param integer close_timeout the timeout for closing modal
         */
        window.open_savecloseModal = function(info, save_callback, title, close_timeout) {
            if(typeof(title) === "undefined") title = "Dialog";
            var save = default_modal_button.get();
            var cancel = default_modal_button.get();
            save.callback = save_callback;
            save.html = "Save";
            cancel.html = "Close";
            save.attrib[0].value = "save";
            cancel.attrib[0].value = "close";
            cancel.cssClass[0] = "btn-default";
            open_modal("div#dialog-modal", info, '<span class="glyphicon glyphicon-tasks"></span> '+title, close_timeout, [cancel, save]);
        };
    })(jQuery);
</script>
<!-- general modal -->
<div id="dialog-modal" class="modal fade" role="general-dialog" aria-labelledby="ops-container-info" aria-hidden="true" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer"></div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="wait-modal" class="modal fade" role="wait-dialog" aria-labelledby="wait-container-info" aria-hidden="true" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-body"><div class='text-center'><img src='/access/img/ajax-loader.gif' /></div></div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php
    }
}