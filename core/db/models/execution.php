<?php
namespace core\db\models;

/**
 * Note Entity
 */
class execution extends \core\db\models\baseModel
{
    /**
     * record the load time
     * @param \core\utiles\loadTime $lt
     * @return integer the recorded time
     */
    public function record(\core\utiles\loadTime $lt)
    {
        $time = $lt->stop();
        $this->query("INSERT INTO `executions` values(?, NOW())", array($time));
        return $time;
    }
    /**
     * get average load time 
     * @return integer in seconds
     */
    public function get_average_load_time()
    {
        $result = ($this->find_by_sql("SELECT ROUND(AVG( x.time ), 5) as time
FROM (
SELECT `executions`.`time`
FROM  `executions`
)x"));
        $result = $result[0];
        return $result->time;
    }
}