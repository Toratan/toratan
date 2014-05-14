<?php
namespace core\db\models;

/**
 * Profile Entity
 */
abstract class baseModel extends \ActiveRecord\Model
{
    /**
     * The save procedure interface for item
     * @param boolean $validate should it validate the attribs
     * @throws \zinux\kernel\exceptions\invalidOperationException if duplication error happen
     * @throws \core\db\models\Exception if any other error happen
     */
    public function save($validate = true)
    {
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
            # if it is a solo-error just throw it
            if(count($this->errors->full_messages())==1)
                throw new \zinux\kernel\exceptions\dbException(array_shift($this->errors->full_messages()));
            
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