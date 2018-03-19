<?php
namespace Core\Validation;
/**
 * Description of Validation
 *
 * @author JackRabbit
 */
//use Core\Core;
//use Core\Helper\Arr;
//use Core\I18n;

class Response extends \Core\Pattern\DataIterator
{
//    public $name;
//    public $status;
//    public $code;
//    public $msg;
//    public $value;
    
    public function __construct($name)
    {
        $this->name = $name;
    }
        
    public function set_default(array $data)
    {
        $this->data = array_merge($data, $this->data);
    }
    
    public function get_array()
    {
        return $this->data;
    }
}
