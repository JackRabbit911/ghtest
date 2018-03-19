<?php
namespace Core;
/**
 * Description of Validation
 *
 * @author JackRabbit
 */
use Core\Core;
use Core\Helper\Arr;
//use Core\Validation\Response;
//use Core\I18n;

class Validation
{
//    use \Core\Validation\ValidLib;
    
    public static $helper = 'Core\Helper\Validation';
    
    public $_rules = array();
    public $replace = [];
//    public $fields = array();
//    public $post = array();
    
    public $response = [];
      
    public function __construct(array $rules = [])
    {
        $this->rules($rules);
    }
    
    public function rule()
    {  
        $args = func_get_args();
        $name = array_shift($args);
        
        if(!empty($args))
        {
            if(!isset($this->_rules[$name])) $this->_rules[$name] = [];
            
//            var_dump($args); exit;
            
            
            $rule = static::parseArray($args);
            $this->_rules[$name] = Arr::merge($this->_rules[$name], $rule);
            unset($rule);
        }
        
        return $this;
    }
    
    public function rules(array $rules = NULL)
    {
        if($rules === NULL) return $this->_rules;
        
        $res = [];
        foreach($rules AS $name => $item)
        {
            if(!is_array($item)) $item = [$item];
            $res[$name] = static::parseArray($item);
        }
        
        $this->_rules = Arr::merge($this->_rules, $res);
        unset($res);
        
        return $this;
    }
    
    public function checkField($name, $value)
    {
        if(!isset($this->_rules[$name])) $this->_rules[$name] = [];
        $this->name_field = $name;
        $ok = TRUE;
        
        foreach($this->_rules[$name] AS $rule)
        {
            if(!isset($rule['args'])) $rule['args'] = [];
            array_unshift($rule['args'], $value);
            
            $class = $rule['func'][0];
            $method = Arr::get($rule['func'], 1);
            
            if(is_object($class)) $object = $class;
            else $object = NULL;
            
            $reflect = new \ReflectionMethod($class, $method);
            
            foreach($reflect->getParameters() AS $param)
            {
                $pos = $param->getPosition();
                
                if(!isset($rule['args'][$pos]))
                {
                    $rule['args'][$pos] = $param->getDefaultValue();
                }
                
                $this->replace[$name][':'.$param->getName()] = $rule['args'][$pos];                
            }
            
                $replace = [
                ':valid'    => & $this,
                ':name'     => $name,
            ];
            
            $rule['args'] = Arr::replace_values($rule['args'], $replace);
            
            $ok = $reflect->invokeArgs($object, $rule['args']);
            
//            var_dump($ok); echo ' -- '; var_dump($object);
           
            if($ok === FALSE)
            {
                if(empty($this->response[$name]['code']))
                    $this->response[$name]['code'] = $method;
                break;
            }
        }
        
        if($ok === FALSE)
        {
            if(empty($this->response[$name]['status']))
                    $this->response[$name]['status'] = 'error';
            $this->response[$name]['value'] = NULL;
            $this->replace[$name] = Arr::flatten($this->replace[$name]);
        }
        else
        {
            $this->response[$name]['value'] = $value;
            if(empty($this->response[$name]['status']))
                    $this->response[$name]['status'] = 'success';           
        }        
        return $ok;
    }
    
    public function check($post)
    {
        $ok = TRUE;
        foreach($post AS $name => $value)
        {
            if($this->checkField($name, $value) === FALSE) $ok = FALSE;
        }
        return $ok;
    }
    
    public function message($name, array $replace = NULL)
    {
        if(isset($this->response[$name]['msg'])) return $this->response[$name]['msg'];
        
        if(!isset($this->replace[$name]))
            $this->replace[$name] = [];
        
        if(!$replace) $replace = $this->replace[$name];
        
        return (isset($this->response[$name]['code'])) ? Core::message($this->response[$name]['code'], $replace) : NULL;      
    }

    public static function parseArray($array, $level=0)
    {
        $res = [];
        $kf = 0;
        $key = 0;
        
        foreach($array AS $value)
        {           
            if($dt = static::is_datatype($value))
            {
                $key = $key+count($dt)-1;
                $res = Arr::merge($res, $dt);
            }
            elseif($func = static::is_func($value))
            {
                $res[$key]['func'] = $func;
                $kf = $key;
            }
            elseif(is_array($value) && static::is_func(current($value)) !== FALSE)
            {
                $sub = static::parseArray ($value, $level+1);
                $res = Arr::merge($res, $sub);
            }
            else $res[$kf]['args'][] = $value;
            
            $key++;
        }
        
        return $res;
    }
    
    public static function is_func($value)
    {
        if(is_callable($value))
        {
            if(is_string($value) && strpos($value, '::') !== FALSE)
            {
                $value = explode('::', $value);
                return $value;
            }
        }
        
        if(is_string($value) && strpos($value, '::') === FALSE && is_callable([static::$helper, $value])) 
                return [static::$helper, $value];
        
//        if(strpos($value, '::') !== FALSE && !is_callable($value)) die($value);
        
        return FALSE;
    }
    
    public static function is_datatype($value)
    {       
        if(is_array($value)) return FALSE;
        
        $datatype = Core::config('datatypes', $value, TRUE);
        
        if($datatype === NULL) return FALSE;
        
        return static::parseArray($datatype);
    }  
    
    
}