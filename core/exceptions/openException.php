<?php
namespace core\exceptions;
/**
 * An open exception which you can set every property of PHP's standard exception class
 */
class openException
{
    public $message;
    public $code;
    public $file;
    public $line;
    public $trace;
    public $previous;
    /**
    * (PHP 5 &gt;= 5.1.0)<br/>
    * Clone the exception
    * @link http://php.net/manual/en/exception.clone.php
    * @return void No value is returned.
    */
   final private function __clone () {}

   /**
    * (PHP 5 &gt;= 5.1.0)<br/>
    * Construct the exception
    * @link http://php.net/manual/en/exception.construct.php
    * @param $message [optional]
    * @param $code [optional]
    * @param $previous [optional]
    */
   public function __construct ($message, $code, $previous) {
       $this->message = $message;
       $this->code = $code;
       $this->previous = $previous;
   }

   /**
    * (PHP 5 &gt;= 5.1.0)<br/>
    * Gets the Exception message
    * @link http://php.net/manual/en/exception.getmessage.php
    * @return string the Exception message as a string.
    */
   final public function getMessage () { return $this->message; }

   /**
    * (PHP 5 &gt;= 5.1.0)<br/>
    * Gets the Exception code
    * @link http://php.net/manual/en/exception.getcode.php
    * @return mixed the exception code as integer in
    * <b>Exception</b> but possibly as other type in
    * <b>Exception</b> descendants (for example as
    * string in <b>PDOException</b>).
    */
   final public function getCode () { return $this->code; }

   /**
    * (PHP 5 &gt;= 5.1.0)<br/>
    * Gets the file in which the exception occurred
    * @link http://php.net/manual/en/exception.getfile.php
    * @return string the filename in which the exception was created.
    */
   final public function getFile () { return $this->file; }

   /**
    * (PHP 5 &gt;= 5.1.0)<br/>
    * Gets the line in which the exception occurred
    * @link http://php.net/manual/en/exception.getline.php
    * @return int the line number where the exception was created.
    */
   final public function getLine () { return $this->line; }

   /**
    * (PHP 5 &gt;= 5.1.0)<br/>
    * Gets the stack trace
    * @link http://php.net/manual/en/exception.gettrace.php
    * @return array the Exception stack trace as an array.
    */
   final public function getTrace () { return $this->trace; }

   /**
    * (PHP 5 &gt;= 5.3.0)<br/>
    * Returns previous Exception
    * @link http://php.net/manual/en/exception.getprevious.php
    * @return Exception the previous <b>Exception</b> if available
    * or <b>NULL</b> otherwise.
    */
   final public function getPrevious () { return $this->previous; }

   /**
    * (PHP 5 &gt;= 5.1.0)<br/>
    * Gets the stack trace as a string
    * @link http://php.net/manual/en/exception.gettraceasstring.php
    * @return string the Exception stack trace as a string.
    */
   final public function getTraceAsString () { ob_start(); print_r($this->getTrace()); return ob_get_clean(); }

   /**
    * (PHP 5 &gt;= 5.1.0)<br/>
    * String representation of the exception
    * @link http://php.net/manual/en/exception.tostring.php
    * @return string the string representation of the exception.
    */
   public function __toString () { return $this->getMessage(); }
}
