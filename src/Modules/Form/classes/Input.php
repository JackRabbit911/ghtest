<?php
namespace Form;
/**
 * Description of Form
 *
 * @author JackRabbit
 */

//use Core\Helper\Arr;
//use Core\View;
//use Core\Validation;
//use Core\Core;

class Input extends Field
{
//    public $name;
//    protected $view_path = 'default/';
//    protected $view_name;
//    protected $selector = 'input';
//    protected $type = 'text';
//    
//    protected $css = array();
//    protected $js = array();
//    protected $rules = array();
//    
//    protected $attr = array(
//        'name'  => NULL,
//        'type'  => NULL,
//        'label' => NULL,
//        'value' => NULL,
//        'plh'   => NULL,
//        'class' => [],
//        'id'    => NULL,
//    );
    
    public static function factory($name, $view_name=NULL)
    {
        return new static($name, $view_name);
    }
    
    public function __construct($name, $view_name=NULL)
    {   
        parent::__construct($name, $view_name);
    }
    
}
