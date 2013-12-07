<?php
namespace core\utiles;
/**
 * Handles a pipe line for messages
 */
class messagePipe extends \zinux\kernel\utilities\pipe
{
    public function __construct()
    {
        parent::__construct(__CLASS__);
    }
}
