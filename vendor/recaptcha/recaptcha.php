<?php
namespace vendor\recaptcha;

require_once 'recaptchalib.php';
class recaptcha {
    /**
     * Holds last error values
     * @var ReCaptchaResponse
     */
    private $last_error;
    /**
     * Construct a re-captcha
     * @return recaptcha $this
     */
    public function __construct()
    {
        $this->last_error = NULL;
        return $this;
    }
    /**
     * Get recaptcha's public key
     * @link https://www.google.com/recaptcha/admin Keys fetched from
     * @return string
     */
    public function get_public_key() {
        return "6LeIavYSAAAAAELJG0qmgopfgKXWedf49Wb25ow6";
    }
    /**
     * Get recaptcha's private key
     * @link https://www.google.com/recaptcha/admin Keys fetched from
     * @return string
     */
    public function get_private_key() {
        return "6LeIavYSAAAAAMzaFdGKPsPu4uPrf6tilitDzRoH";
    }
    /**
     * Render a recaptcha html frame
     */
    public function __render_html($theme = "clean") {
        echo "<script type='text/javascript'>var RecaptchaOptions = { theme : '$theme' };</script>";
        echo recaptcha_get_html($this->get_public_key(), $this->last_error);
    }
    /**
     * Render a recaptcha javascript frame
     */
    public function __render_JS($theme = "clean") {
        echo 
        "<div id='recaptchadiv'><b  style='font-variant:small-caps;font-size:large'>Loading Recaptcha....</b></div>
        <script type='text/javascript'>
            $(function() { 
                $.getScript( '//www.google.com/recaptcha/api/js/recaptcha_ajax.js', function() {
                    Recaptcha.create('{$this->get_public_key()}', 'recaptchadiv', {theme: '$theme'});
                });
            });
        </script>";
    }
    /**
     * check if recaptcha sent by $_REQUEST is valid 
     */
    public function is_recaptcha_valid() {
        $resp = recaptcha_check_answer ($this->get_private_key(),
                                      $_SERVER["REMOTE_ADDR"],
                                      @$_REQUEST["recaptcha_challenge_field"],
                                      @$_REQUEST["recaptcha_response_field"]);

        if (!$resp->is_valid) {
            $this->last_error = $resp->error;
            return false;
        } else {
            $this->last_error = NULL;
            return true;
        }
    }
    /**
     * Get last error
     * @return ReCaptchaResponse
     */
    public function get_last_error() {
        return $this->last_error;
    }
    /**
     * invokes mail hide functionality of google recaptcha
     * @param string $email
     * @param boolean $html_version (default: true) flag it to whether or not to use html version of mailhide api or not 
     */
    public function mailhide($email, $html_version = 1) {
        /**
         * For mail hide there are different type of keys to use
         * got keys at http://www.google.com/recaptcha/mailhide/apikey
         */
        $mailhide_pub_key = "01AqRGul3OsqgOby5PA1vd9w==";
        $mailhide_prv_key = "3ebddcb55a46385363337153219641ad";
        $func = "recaptcha_mailhide_html";
        if(!$html_version)
            $func ="recaptcha_mailhide_url";
        return $func($mailhide_pub_key, $mailhide_prv_key, $email);
    }
}