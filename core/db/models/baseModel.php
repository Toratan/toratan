<?php
namespace core\db\models;

/**
 * Profile Entity
 */
abstract class baseModel extends \ActiveRecord\Model
{
    /**
     * Flag that if save disabled for current instance
     * @var boolean TRUE if save disabled; otherwise FALSE
     */
    private $save_disabled;
    /**
     * Prepares and escapes an array for an IN(?) query
     * @param array $in The in query array
     * @param boolean $prepare_for_inline_injection (default: true) If you are binding the result of this function 
     * to the query pass false, But if you are injecting it like `... IN($result_of_escape_in_query_func) ...` pass true.
     * @return string The IN(?) query value
     */
    public static function escape_in_query(array $in, $prepare_for_inline_injection = 1) {
        # glutize the array an fetch the string format of INs
        $in = implode(", ", $in);
        # secure(escape) the IN and re-normalize it to inject directly into QUERY 
        $in = implode("', '", explode(", ", substr(self::connection()->escape($in), 1, strlen($in))));
        # if we are preparing for inline injection?
        if($prepare_for_inline_injection)
            # queto it
            return "'$in'";
        # return pure, because PAR will quetofy the text as well
        return $in;
    }
    /**
     * get sql builder according to current model of use
     * @return \ActiveRecord\SQLBuilder
     */
    protected static function getSQLBuilder() {
        return new \ActiveRecord\SQLBuilder(self::connection(), self::table_name());
    }
    /**
     * @see \ActiveRecord\Model::find()
     */
    public static function find()
    {
        try {
            return call_user_func_array("parent::find", func_get_args());
        }catch(\ActiveRecord\RecordNotFound $rnf) {
            throw new \zinux\kernel\exceptions\notFoundException($rnf->getMessage());
        }
    }
    /**
     * Flag that the current is prevented from being saved into database
     * @param boolean $save_disabled TRUE if save disabled; otherwise FALSE
     */
    public function disableSave($save_disabled = true) { $this->save_disabled = $save_disabled; }
    /**
     * The save procedure interface for item
     * @param boolean $validate should it validate the attribs
     * @throws \zinux\kernel\exceptions\invalidOperationException if duplication error happen
     * @throws \core\db\models\Exception if any other error happen
     */
    public function save($validate = true)
    {
        if($this->save_disabled)
            throw new \zinux\kernel\exceptions\invalidOperationException("The `save()` operation is disabled for current item.");
        try
        {
            # try to save it
            parent::save($validate);
        }
        # cache if anything happened
        catch(\Exception $e)
        {
            # if it was a duplication error
            if(preg_match("#1062 Duplicate entry#i", $e->getMessage()))
                    # throw an invalid operation exception
                    throw new \core\db\exceptions\alreadyExistsException("Entity already exists!");
            # otherwise throw just as is
            else throw $e;
        }
        # to boost up the speed we don't put it in the try/catch to prevent to getting thrown twice
        # check if it is an invalid
        if($this->is_invalid())
        {
            # create an exception collector
            $ec = new \core\exceptions\exceptionCollection;
            foreach($this->errors->full_messages() as $error_msg)
            {
                # add the message as an exception in the collector
                $ec->addException(new \zinux\kernel\exceptions\dbException($error_msg));
            }
            # throw the exceptions
            $ec->ThrowCollected();
        }
    }
}