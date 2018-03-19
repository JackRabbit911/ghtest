<?php
namespace Core\DB;

/**
 * Description of Expression
 *
 * @author JackRabbit
 */

use Core\DB;

class Expression
{
    public $expr;
    
    public function __construct($str)
    {
        $this->expr = $str;
    }
    
    public function __toString()
    {
        return $this->expr;
    }
}
