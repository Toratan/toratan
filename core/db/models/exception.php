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
        $t = $e->getTrace();
        $this->exception_code = $id;
        $this->exception_type = get_class($e);
        $this->exception_data =serialize($e);
        $this->occurrence_file = @$t[0]["file"];
        $this->occurrence_line = @$t[0]["line"];
        $this->user_id = @user::GetInstance()->user_id;
        $this->save();
        return $this->exception_id;
    }
}