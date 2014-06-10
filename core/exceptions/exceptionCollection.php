<?php
namespace core\exceptions;

class exceptionCollection extends \zinux\kernel\exceptions\appException
{
    /**
     * Exception collection
     * @var \Exception
     */
    protected $collection = array();
    /**
     * Add a new exception to exception collection
     * @param \Exception $exception
     */
    public function addException(\Exception $exception)
    {
        # if it is null do not add it to collection
        if(!$exception) return;
        # add the exception to collection
        $this->collection[] = $exception;
    }
    /**
     * Get exception collection that current instance contains
     * @return array of \Exception
     */
    public function getCollection() { return $this->collection; }
    /**
     * Get count of exception collection that current instance contains
     * @return integer
     */
    public function getCollectionCount() { return count($this->collection); }
    /**
     * If any exception has been collection with this instance throw the collection
     * @throws exceptionCollection
     */
    public function ThrowCollected() { if(count($this->collection)) throw $this; }
}
