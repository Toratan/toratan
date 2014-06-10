<?php
namespace core\utiles;
/**
 * Calculate load time
 */
class loadTime
{
    /**
     * the interval start time
     * @var integer
     */
    private $time_start     =   0;
    /**
     * Start the interval
     */
    public function start(){ $this->time_start= microtime(true); }
    /**
     * Stop the interval and get exceuted time
     * @return integer the second elapsed
     */
    public function stop(){ return  round(microtime(true) - $this->time_start, 5); }
}