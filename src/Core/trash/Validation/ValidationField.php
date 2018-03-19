<?php
namespace Core\Validation;
/**
 * Description of Validation
 *
 * @author JackRabbit
 */
use Core\Validation;
use Core\Core;
use Core\Helper\Arr;
//use Core\I18n;

class ValidationField
{
    public $name;
    public $_rules = [];
    public $replace = [];
    public $status;
    public $code;
    public $validation;
//    public $msg;
    public static $values = [];
    
    public function __construct($name, $rules = [], $validation)
    {
        $this->name = $name;
        $this->_rules = $rules;
//        $this->replace[':field'] = $name;
        $this->validation = $validation;
    }
        
//    public function rule()
//    {
//        
//    }
    
    public function rules($rules)
    {
        if($rules === NULL) return $this->_rules;
        
        $res = [];
        foreach($rules AS $item)
        {
            if(!is_array($item)) $item = [$item];
            $r = Validation::parseArray($item);
            
//            print_r($r);
            
            $res[] = $r[0];
        }
        
        $this->_rules = Arr::merge($this->_rules, $res);
        unset($res);
        unset($rules);
        
        return $this;
    }
    
    public function check($value)
    {
        
        print_r($this->_rules); exit;
        
        foreach($this->_rules AS $rule)
        {
            $class = $rule['func'][0];
            $method = Arr::get($rule['func'], 1);
            
            $reflect = new \ReflectionMethod($class, $method);
            
            if(is_array($rule['func']) && isset($rule['func'][0]) && is_object($rule['func'][0]))
                $object = $rule['func'][0];
            else $object = NULL;
            
            if(!isset($rule['args'])) $rule['args'] = [];
            array_unshift($rule['args'], $value);
            
            
//            var_dump($object); exit();
            
//            static::$values[$name] = $value;
            
            $this->replace[':field'] = $this->name;
            
            $k = 0;
            
            foreach($reflect->getParameters() AS $param)
            {
//                if($param->getName() === 'validation')
//                {
//                    array_unshift($rule['args'], $this->validation);
//                }
//                
//                var_dump($rule['args']);
//                echo '<hr>';
                
                $this->replace[':'.$param->getName()] = $rule['args'][$k];
                
//                var_dump($param->getPosition());
                $k++;
            }
            
            
            
            $ok = $reflect->invokeArgs($object, $rule['args']);
            
            var_dump($ok); echo '<br>';
            
            if($this->status === NULL && $ok === FALSE) $this->status = 'error';
            elseif($this->status === NULL && $ok !== FALSE) $this->status = 'success';
            
            if(!$this->code) $this->code = $reflect->getName();
            
            
            
            if($ok === FALSE) return $ok;
            
        }
        
//        var_dump($this->replace);
        
        return $ok;
    }
    
    public function message($code = NULL, array $replace = NULL)
    {
//        if(!isset($this->response[$name]['replace']))
//            $this->response[$name]['replace'] = [];
        
        
//        var_dump($this->replace); exit;
         
        if(!$code) $code = $this->code;
        
        if($replace) $this->replace = Arr::merge($this->replace, $replace);
        
        return Core::message($code, $this->replace);      
    }
}
