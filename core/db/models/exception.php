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
        $this->exception_data =serialize($e);
        $this->occurrence_file = $e->getFile();
        $this->occurrence_line = $e->getLine();
        $this->user_id = @user::GetInstance()->user_id;
        $this->save();
        return $this->exception_id;
    }
}