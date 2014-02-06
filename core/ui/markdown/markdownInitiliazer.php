<?php
namespace core\ui\markdown;
class markdownInitiliazer extends \zinux\kernel\application\baseInitializer
{
    public function Execute()
    {
        # including markdown functions
        require_once 'lib/markdown.php';
    }    
}