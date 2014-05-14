<?php
namespace core\ui\html;

/**
 * prints an html alert
 */
class alert extends htmlPrinter
{
    const ALERT_SUCCESS = "alert-success";
    const ALERT_INFO = "alert-info";
    const ALERT_DANGER = "alert-danger";
    const ALERT_DISMISSABLE = "alert-dismissable";
    const ALERT_LINK = "alert-link";
    const ALERT_WARNING = "alert-warning";

    /**
     * Print the passed data with twitter-bootstrap style alert
     * @param string $data
     * @param string $alert_type one of \core\ui\html\alert::ALERT_* flags
     * @param boolean $immediate_echo set TRUE if should echo the output[default]; otherwise returns the generated alert
     * @param boolean $auto_dispose set TRUE if should the output get disposed after 10 seconds[default]; otherwise returns the generated alert
     * @return string if $immediate_echo == 0 returns the alert string
     * @throws \zinux\kernel\exceptions\invalidArgumentException if input is not string
     */
    public static function Cout($data, $alert_type = alert::ALERT_INFO, $auto_dispose = 1, $immediate_echo = 1, $show_time = 7000)
    {
        if(!is_string($data))
            throw new \zinux\kernel\exceptions\invalidArgumentException;
        $alert = 
            "<div class='alert fade in $alert_type ' style='z-index:10000000;border: 1px solid;padding: 1%;margin:0.5%;' id='".($id="alert-".sha1($data))."'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true' >&times;</button>
                <p class='text-center inline' style='margin:0;padding:0'>$data</p>
            </div>
            <script id='$id' type='text/javascript'>
                $(document).ready(function(){
                    \$(\"div#$id\").hide().slideDown(\"slow\");
                });
            </script>
            ";
        if($auto_dispose)
            $alert .= "<script id='$id' type='text/javascript'>
                $(document).ready(function(){
                    setTimeout('\$(\"div#$id\").slideUp(\"slow\");\$(\"script#$id\").remove()', $show_time);
                });
            </script>";
        if($immediate_echo)
            echo $alert;
        else
            return $alert;
    }
}