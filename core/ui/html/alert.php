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
     * @return string if $immediate_echo == 0 returns the alert string
     * @throws \zinux\kernel\exceptions\invalideArgumentException if input is not string
     */
    public static function Cout($data, $alert_type = alert::ALERT_INFO, $immediate_echo = 1)
    {
        if(!is_string($data))
            throw new \zinux\kernel\exceptions\invalideArgumentException;
        $alert = 
            "<div class='alert fade in $alert_type ' style='min-height: 50px;border: 1px solid #00E0F0;padding: 15px;margin:10px;'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <p class='text-center inline' style='margin:0;padding:0'>$data</p>
            </div>";
        if($immediate_echo)
            echo $alert;
        else
            return $alert;
    }
}