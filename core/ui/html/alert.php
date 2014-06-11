<?php
namespace core\ui\html;

/**
 * prints an html alert
 */
class alert
{
    const ALERT_SUCCESS = "success";
    const ALERT_INFO = "info";
    const ALERT_DANGER = "danger";
    const ALERT_DISMISSABLE = "dismissable";
    const ALERT_LINK = "link";
    const ALERT_WARNING = "warning";

    const PIPE_NONE = "";
    const PIPE_SUCCESS = "success";
    const PIPE_INFO = "info";
    const PIPE_WARNING = "warning";
    const PIPE_DANGER = "danger";
    
    /**
     * Print the passed data with twitter-bootstrap styled alert-*
     * @param string $data
     * @param string $alert_type one of \core\ui\html\alert::ALERT_* flags
     * @param boolean $auto_dispose set TRUE if should the output get disposed after 10 seconds[default]; otherwise returns the generated alert
     * @param boolean $immediate_echo set TRUE if should echo the output[default]; otherwise returns the generated alert
     * @return string if $immediate_echo == 0 returns the alert string
     * @throws \zinux\kernel\exceptions\invalidArgumentException if input is not string
     */
    public static function Cout($data, $alert_type = self::ALERT_INFO, $auto_dispose = 1, $immediate_echo = 1, $show_time = 7000)
    {
        if(!is_string($data))
            throw new \zinux\kernel\exceptions\invalidArgumentException;
        switch($alert_type){
            case self::ALERT_DANGER:
            case self::ALERT_DISMISSABLE:
            case self::ALERT_INFO:
            case self::ALERT_LINK:
            case self::ALERT_SUCCESS:
            case self::ALERT_WARNING:
                break;
            default: throw new \zinux\kernel\exceptions\invalidArgumentException;
        }
        return self::out(
            "<div class='alert fade in alert-$alert_type ' style='z-index:10000000;border: 1px solid;padding: 1%;margin:0.5%;' id='".($id="alert-".sha1($data))."'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <p class='text-center inline' style='margin:0;padding:0'>$data</p>
            </div>
            <script id='$id' type='text/javascript'>
                $(document).ready(function(){
                    \$(\"div#$id\").hide().slideDown(\"slow\");
                });
            </script>
            ", $id, $auto_dispose, $immediate_echo, $show_time);
    }
    /**
     * Print the passed data with twitter-bootstrap styled text-*
     * @param string $data
     * @param string $alert_type one of \core\ui\html\alert::PIPE_* flags
     * @param boolean $auto_dispose set TRUE if should the output get disposed after 10 seconds[default]; otherwise returns the generated alert
     * @param boolean $immediate_echo set TRUE if should echo the output[default]; otherwise returns the generated alert
     * @return string if $immediate_echo == 0 returns the text string
     * @throws \zinux\kernel\exceptions\invalidArgumentException if input is not string
     */
    public static function Tout($data, $pipe_type = self::PIPE_NONE, $auto_dispose = 1, $immediate_echo = 1, $show_time = 7000) {
        if(!is_string($data))
            throw new \zinux\kernel\exceptions\invalidArgumentException;
        switch($pipe_type) {
            case self::PIPE_NONE:
            case self::PIPE_SUCCESS:
            case self::PIPE_DANGER:
            case self::PIPE_INFO:
            case self::PIPE_WARNING:
                break;
            default:
                throw new \zinux\kernel\exceptions\invalidArgumentException;
        }
        return self::out(
            "<div class='text-justify text-center text-$pipe_type' id='".($id="pipe-".sha1($data))."'>
                <style class='.error-container' type='text/css'>
                    .pipe-msg {width: 100%;padding: 20px;margin: 10px auto;border: 1px solid #cccccc; font-weight:bold;text-align:center}
                </style>
                <div class='pipe-msg text-$pipe_type text-center '>
                    <span class='glyphicon glyphicon-chevron-right'></span> $data
                </div>
            </div>
            ", $id, $auto_dispose, $immediate_echo, $show_time);
    }
    /**
     * General output ops for this class
     * @param type $data
     * @param type $id The id of created element
     * @param boolean $auto_dispose set TRUE if should the output get disposed after 10 seconds[default]; otherwise returns the generated alert
     * @param boolean $immediate_echo set TRUE if should echo the output[default]; otherwise returns the generated alert
     * @return string if $immediate_echo == 0 returns the alert string
     * @return string if $immediate_echo == 0 returns the data string
     */
    protected static function out($data, $id, $auto_dispose = 1, $immediate_echo = 1, $show_time = 7000) {
        $data .= "<script id='$id' type='text/javascript'>
            $(document).ready(function(){
                \$(\"div#$id\").hide().slideDown(\"slow\");";
        if($auto_dispose)
                $data .= "setTimeout('\$(\"div#$id\").slideUp(\"slow\");\$(\"script#$id\").remove()', $show_time);";
        $data .= "});</script>";
        if($immediate_echo)
            echo $data;
        else
            return $data;
    }
}