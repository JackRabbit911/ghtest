<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Api
 *
 * @author JackRabbit
 */
namespace Docs\Model;

use Core\Core;
use Core\Helper\Arr;

class Api
{
    public $modules = [];
    
    public function modules($uri_string = NULL)
    {
        if($uri_string)
        {
            $module_name = str_replace('_', '\\', $uri_string);
            $column = array_column($this->modules, 'module');
            $column = array_map('strtolower', $column);
            $key = array_search($module_name, $column);
            return ($key !== FALSE && isset($this->modules[$key])) ? $this->modules[$key] : FALSE;
        }
        else
        {
            if(empty($this->modules))
            {
                $i = 0;
                foreach(Core::$_paths AS $path=>$namespace)
                {
                    $module_name = (string) substr($path, strlen(SRCPATH));
                    $this->modules[$i]['link'] = mb_strtolower(str_replace('\\', '_', $module_name));
                    $this->modules[$i]['module'] = $module_name;
                    $this->modules[$i]['path'] = (string)substr($path, strlen(DOCROOT));
                    $this->modules[$i]['namespace'] = $namespace;
                    $i++;
                }
            }
            return $this->modules;
        }
    }
    
    public function get_classes($dir, $prefix)
    {
        $classes = $this->_get_classes($dir, $prefix);
        $result = [];
        foreach($classes AS $key=>$class)
        {
            $result[$key]['class'] = $class;
            $result[$key]['link'] = strtolower($prefix).'/'.str_replace('\\', '_', strtolower(substr($class, strlen($prefix)+1)));
        }
        return $result;
    }
    
    protected function _get_classes($dir, $prefix)
    {
        $result = [];
        
//        return glob($dir.DIRECTORY_SEPARATOR.'*');
        
        
        
        foreach(glob($dir.DIRECTORY_SEPARATOR.'*') AS $key=>$filepath)
        {
            $file = (string) substr($filepath, strlen($dir)+1);
            $class = pathinfo($file, PATHINFO_FILENAME);
            
//            $result[] = $class;
            
            if(is_dir($filepath)) $result[] = $this->_get_classes($filepath, $prefix.'\\'.$class);
            else
            {
                
                $result[] = $prefix.'\\'.$class;
//                $result[] = $class;
            }
        }
        
//        return $result;
        
        $result = Arr::flatten($result);
        asort($result);
        
     
        return $result;
    }
    
    
    
    public function class_info($class_name)
    {
        if(!isset($this->class))
            $this->class = new \ReflectionClass($class_name);
        
        return $this->class->getMethods();
        
    }
}
