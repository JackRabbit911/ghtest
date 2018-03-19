<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Docs;

/**
 * Description of sysInfo
 *
 * @author JackRabbit
 */

use Core\Core;
use Core\Helper\Arr;

class ModuleInfo
{
    
    public static function module($module_url)
    {
        $module = [];
        $module['url'] = $module_url;
        
        $name = str_replace('_', '\\', $module_url);
        
        $array = array_filter(Core::paths(), function($k) use (&$name) {
            $str = strtolower(substr($k, strlen(SRCPATH)));
//            if(stripos($str, $name) !== FALSE) return TRUE;
            if($str === $name) return TRUE;
            else return FALSE;
        }, ARRAY_FILTER_USE_KEY);
        
        if(count($array) === 1)
        {
            $key = key($array);
            $module['name'] = (string) substr($key, strlen(SRCPATH));
            $module['namespace'] = $array[$key];
            $module['path'] = $key;
        }
//        else
//        {
//            print_r($array); exit;
//        }
        
        return $module;
    }
    
    public static function classes($dir, $namespace)
    {
        $dir .= DIRECTORY_SEPARATOR.'classes';
        $prefix = str_replace('\\', '_', $namespace);
        $classes = static::_get_classes($dir, $namespace);
        $result = [];
        foreach($classes AS $key=>$class)
        {
            $result[$key]['class'] = $class;
            $result[$key]['link'] = $prefix.'_'.str_replace('\\', '_', substr($class, strlen($prefix)+1));
        }
        return $result;
    }
    
    protected static function _get_classes($dir, $namespace)
    {
        $result = [];
        
        foreach(glob($dir.DIRECTORY_SEPARATOR.'*') AS $key=>$filepath)
        {
            $file = (string) substr($filepath, strlen($dir)+1);
            $class = pathinfo($file, PATHINFO_FILENAME);
         
            if(is_dir($filepath)) $result[] = static::_get_classes($filepath, $namespace.'\\'.$class);
            else
            {                
                $result[] = $namespace.'\\'.$class;
            }
        }
        
        $result = Arr::flatten($result);
        asort($result);
             
        return $result;
    }
}
