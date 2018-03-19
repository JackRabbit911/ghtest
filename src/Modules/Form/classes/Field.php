<?php
namespace Form;
/**
 * Description of Form
 *
 * @author JackRabbit
 */

use Core\Helper\Arr;
use Core\View;
use Core\Validation;
use Core\Core;

abstract class Field
{
    use FormField;
    
    public static $class = [
        'error'     => 'has-error',
        'warning'   => 'has-warning',
        'success'   => 'has-success',
    ];

    protected $view_name = 'input';
//    protected $type = 'text';
    
//    public $validation;
    
    public $response;
    public $_rules = array();   
    public $attr = array(
        'name'  => NULL,
        'type'  => 'text',
        'label' => NULL,
        'value' => NULL,
        'plh'   => NULL,
        'class' => NULL,
        'id'    => NULL,
        'msg'   => NULL,
        'disabled'=>NULL,
    );
    
    public function __construct($name, $view_name=NULL)
    {
        if($view_name === NULL) 
            $this->view_name = $this->view_path.$this->view_name;
        else $this->view_name = $view_name;
        
        $this->attr['name'] = $name;
        $this->attr['id'] = uniqid().$name;
    }

    public function rules($rules)
    {
        if($rules === NULL) return $this->_rules;
        
        $res = [];
        foreach($rules AS $item)
        {
            if(!is_array($item)) $item = [$item];
            $res[] = Validation::parseArray($item);
        }
        
        $this->_rules = Arr::merge($this->_rules, $res);
        unset($res);
        
        return $this;
    }
    
    public function rule()
    {
        $args = func_get_args();
//        $name = array_shift($args);
        
//        if(!empty($args))
//        {
////            if(!isset($this->_rules[$name])) $this->_rules[$name] = [];
//            $rule = Validation::parseArray($args);
//            $this->_rules = Arr::merge($this->_rules, $rule);
//            unset($rule);
//        }
        
        $this->_rules = Arr::merge($this->_rules, $args);
        
        return $this;
    }
    
//    public function message($replace = [])
//    {
////        var_dump($replace);
////        return $this->attr['name'];
//        
//        return Core::message($this->response['code'], $replace);
//    }
    
    public function status($status, $msg = NULL)
    {        
        $name = $this->attr['name'];
        
        if($status !== 'error')
            $value = $this->validation->response[$name]['value'];
        else $value = NULL;
        
        if($msg === NULL)
        {
            $this->validation->replace[$name][':field'] = $this->attr['label'];
            $msg = $this->validation->message($name);
        }
        
//        if($code === NULL) $code = $this->validation->response[$name]['code'];
        
        
        $this->attr('class', static::$class[$status]);
        
        $this->attr('msg', $msg);
        $this->attr('value', $value);
    }

    public function render()
    {
        
//        var_dump($this->attr);
        
//        $this->attr['class'] = implode(' ', $this->attr['class']);
        
        return View::factory($this->view_name, $this->attr)
                ->css($this->css)
                ->js($this->js)
                ->render();
    }

}
