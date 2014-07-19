<?php
namespace core\db\models;
/**
 * Note Entity
 */
class exception extends \core\db\models\baseModel
{
    /**
     * record an exception into database
     * @param \Exception $e The exception
     * @param intereger $id The exception ID
     * @return string The recorded expcetion reference
     */
    public function record(\Exception $e, $id)
    {
        $this->exception_code = $id;
        $this->exception_type = get_class($e);
        try{
            $this->exception_data = serialize($e);
        }catch(\Exception $_e){
            $oe = new \core\exceptions\openException($e->getMessage());
            $oe->code = $e->getCode();
            $oe->trace = array(@$_SERVER["HTTP_REFERER"]);
            $oe->file = $e->getFile();
            $oe->line = $e->getLine();
            $oe->previous = NULL;
            $this->exception_data = serialize($oe);
        }
        $this->occurrence_file = $e->getFile();
        $this->occurrence_line = $e->getLine();
        $this->user_id = @user::GetInstance()->user_id;
        try {
            # if user's get deleted by any mean
            # it will throw a expcetion
            $this->save();
        }catch(\Exception $pe) {
            # in case of user being deleted!
            if(preg_match("#SQLSTATE\[23000\]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails#i", $pe->getMessage())) {
                $this->user_id = NULL;
                $this->save();
            }
            unset($pe);
        }
        return $this->exception_id;
    }
}