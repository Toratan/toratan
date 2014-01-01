<?php
namespace core\exceptions;
/**
 * upload exception handler
 */
class uploadException extends \zinux\kernel\exceptions\appException
{
    public function __construct($code) {
        $message = $this->codeToMessage($code);
        parent::__construct($message, $code);
    }
    /**
     * Converts upload error codes to its proper message
     * @param integer $code UPLOAD_ERR_*
     * @return string the error message
     */
    protected function codeToMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file size overflow.";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file size overflow.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded.";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder.";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk.";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension.";
                break;
            default:
                $message = "Unknown upload error.";
                break;
        }
        return $message;
    }
} 