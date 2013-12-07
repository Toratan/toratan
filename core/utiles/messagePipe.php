<?php
namespace core\utiles;
/**
 * Handles a pipe line for messages
 */
class messagePipe extends \zinux\kernel\utilities\pipe
{
    public function __construct($name = __CLASS__)
    {
        parent::__construct($name);
    }
}
