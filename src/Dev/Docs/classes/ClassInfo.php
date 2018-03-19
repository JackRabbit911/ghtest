<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Docs;
/**
 * Description of ClassInfo
 *
 * @author JackRabbit
 */
use Core\Core;
use Core\Exception;
use Core\Helper\Arr;
use Docs\DocBlock;
use Docs\CodeBlock;

class ClassInfo extends \ReflectionClass
{
    /** Экземпляр класса */
//    protected $reflector;
//    protected $class_name;
    // output
//    public $class = [];
    protected $class;
    
    protected $modifier = [
        '256'   => 'public',
        '257'   => 'public static',
        '512'   => 'protected',
        '513'   => 'protected static',
        '1024'  => 'private',
        '1025'  => 'private static',
    ];
    
    /**
     * Create object of self
     * 
     * @param type $class_name
     * @return \self
     * @throws Exception\HTTPException
     */
    public static function factory($class_name)
    {
//        $reflector = new \ReflectionClass($class_name);
        if(!class_exists($class_name) && !Core::$errors)
            throw new Exception\HTTPException('not found', 404);
        
        return new self($class_name);    
    }
    
//    public function __construct($reflector)
//    {
////        $this->reflector = $reflector;
//        $this->class['name'] = $reflector->getName();
//        $this->class['namespace'] = $reflector->getNamespaceName();
//        $this->class['parent'] = $this->parent_class($reflector);
//        $this->class['properties'] = $this->properties($this->class['name'], $reflector);
//    }
    
   


    

    public function get()
    {
        $this->parent = $this->getParentClass();
        
        $this->class['file'] = $this->getFileName();
        $this->class['comment'] = $this->_docComment($this->getDocComment());
        $this->class['name'] = $this->getName();
        $this->class['namespace'] = $this->getNamespaceName();
        $this->class['parent'] = $this->_parent_class();
        $this->class['extends'] = $this->_parent_breadcrumbs();
        $this->class['properties'] = $this->_properties();
        $this->class['methods'] = $this->_methods();
        
        return $this->class;
    }
    
    
    
    protected function _docComment($comment)
    {
//        $comment = $this->getDocComment();
        return DocBlock::parse($comment, TRUE);
    }
    
    protected function _parent_class()
    {
        $parent_reflector = $this->getParentClass();
        if(!$parent_reflector) return NULL;
        
        $parent = [];
        $parent['name'] = $parent_reflector->getName();
        $parent['file'] = $parent_reflector->getFileName();
        
        if($parent['file'])
        {
            $str = substr($parent['file'], strlen(SRCPATH));
            $pos = strpos($str, 'classes');
            $module = substr_replace($str, '', $pos);
            $parent['url'] = '/docs/api/'.strtolower($module).strtolower(str_replace('\\', '_', $parent['name']));
            $parent['target_link'] = '';
        }
        else
        {
            $parent['url'] = 'http://php.net/manual/class.'.strtolower($parent['name']).'.php';
            $parent['name'] = '\\'.$parent['name'];
            $parent['target_link'] = 'target="_blank"';
        }
        
        return $parent;
    }
    
    protected function _parent_breadcrumbs()
    {
        $parent = $this->parent;
        $result = '';
        
        while($parent)
        {
            $result .= ', '.$parent->getName();
            $parent = $parent->getParentClass();
        }
        
        return ltrim($result, ', ');
    }


    protected function _properties()
    {
//        $class_name = $this->getName();
        $this->vars = get_class_vars($this->getName());
        
//        $parent = $this->getParentClass();
//        if($parent !== FALSE)
//            $parent_props = $parent->getProperties();
//        else $parent_props = [];
        $result = [];
        if($props = $this->getProperties())
        {
            
            foreach($props AS $prop)
            {
                $result[] = $this->_property($prop);
            }
            
        }
//        else $this->class['properties'] = [];
        
//        return $props;
        
        return $result;
    }
    
    protected function _methods()
    {
        $result = [];
        $methods = $this->getMethods();
        foreach($methods AS $key=>$method)
        {
            $m = $this->_method($method);
            if($m !== FALSE ) $result[$key] = $m;
        }
        
        return $result;
    }
    
    private function _property($property)
    {
        $res['name'] = $property->getName();
        $res['comment'] = DocBlock::parse($property->getDocComment());
        
        $res['modifier'] = Arr::get($this->modifier, $property->getModifiers(), NULL);
        
        if($property->isStatic())
        {
            $property->setAccessible(TRUE);
            $value = $property->getValue();
        }
        else $value = Arr::get($this->vars, $res['name'], FALSE);
        
        
               
        $res['type'] = gettype($value);
        
        if(is_array($value))
        {
            if(empty($value)) $res['value'] = 'array()';
            elseif(!is_array($value)) $res['value'] = implode(', ', $value);
            else $res['value'] = 'Array()';
        }
        elseif($value === NULL) $res['value'] = 'NULL';
        elseif($value === FALSE) $res['value'] = 'FALSE';
        elseif($value === TRUE) $res['value'] = 'TRUE';
        elseif(is_object($value))
        {
//            echo $res['name'].' ';
//            var_dump($value);
            $res['value'] = 'Object()';
        }
        else $res['value'] = $value;
        
        if($this->parent && $this->parent->hasProperty($res['name'])) $res['extends'] = TRUE;           
        else $res['extends'] = FALSE;
        
        return $res;
    }
    
    private function _method($method)
    {
        if($method->getFileName() === FALSE) return FALSE;
        
//        return $method;
        
        $result = [];
        
        if(!$file = $method->getFileName()) return FALSE;
        
        $result['name'] = $method->name;
        
        if($method->isPublic()) $mod1 = 'public';
        elseif($method->isProtected()) $mod1 = 'protected';
        elseif ($method->isPrivate()) $mod1 = 'private';
        else $mod1 = 'public (default)';
        
        if($method->isStatic()) $mod2 = ' static';
        else $mod2 = '';
        
        $result['prefix'] = $mod1.$mod2.' function';
        
        
//        $result['modifiers'] = $method->getModifiers();
        $result['comment'] = $this->_docComment($method->getDocComment());
//        $result['class'] =  $method->getFileName();
        $result['parameters'] = $this->_parameters($method->getParameters());
        
        $result['source'] = DocBlock::source($file, $method->getStartLine(), $method->getEndLine());
        
//        $params = $method->getParameters();
//        $str = '';
//        foreach($params AS $param)
//        {
//            $str .= $param;
//        }
//        $result['parameters'] = $str;
        
        
        
//        $start = $method->getStartLine();
//        $end = $method->getEndLine();        
//        $result['source'] = CodeBlock::source($this->class['file'], $start, $end);
        
        return $result;
    }
    
    private function _parameters($parameters)
    {
        $result = [];
        foreach($parameters AS $parameter)
        {
            $result['name'] = '$'.$parameter->getName();
//                $r = new \ReflectionClass($parameter);
//            $result['type'] = $parameter->isArray(); //$r->getMethods();
        }
        return $result;
    }
}
