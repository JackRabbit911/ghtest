<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Docs\Controller;

//use Docs\Controller;
use Core\Core;
//use Core\Cache;
use Core\Controller\Params;
use Core\Exception;
use Core\Helper\Arr;
use Core\Helper\Text;
//use Docs\Parsedown;
use Docs\ClassInfo;
use Docs\SysInfo;
use Docs\ModuleInfo;

use Docs\ClassApi;

/**
 * Description of Index
 *
 * @author JackRabbit
 */
class Api extends Index//Controller\Template
{
    use Params;
    
    public $params_keys = ['module1'=>'module', 'class'];
    
//    public $modules = [];
    public $model;
    
//    public $template = '/Docs/views/template';
    
    public function index()
    {
//        $menu = Core::config('menu');
//        $this->template->navbar = $this->_view('/Docs/views/navbar', $menu)
//                ->set('params', $this->request->params());
        $this->template->title = 'Webnigger API '.$this->request->params('module1').' '.$this->request->params('class');
        $this->template->content = $this->_call_param_key_method($this->request->params(), $this->params_keys);
        
        $md_file = $this->_get_md_file($this->request->params('module1'), $this->request->params('class')); //($this->request->params('module1')) ? $this->request->params('module1'): 'index';
        
        
        $this->template->content->desc = Text::markdown($md_file, 'docs/api');
        $this->template->js('media/js/wnbs3.js', 'media/js/api.js');
    }
    
    protected function _before()
    {
        parent::_before();
    }

    protected function _index()
    {
//        $desc = Text::markdown($this->name, '/Docs/docs/api');

        $data = [
//            'desc'      => $desc,
            'modules'   => SysInfo::modules(),
        ];
        return $this->_view('api/index', $data);
    }
    
    protected function _module($params)
    {        
        try
        {
            $module = ModuleInfo::module($params['module']);
            
//            print_r($module); exit;
            
            $module['classes'] = ModuleInfo::classes($module['path'], $module['namespace']);
        }
        catch(Exception\Exception $e)
        {
            throw new Exception($e);
        }
        
        $this->template->sidebar = '';
        
        return $this->_view('api/module', $module);
    }
    
    protected function _class($params)
    {
//        echo 'qqq'; exit;
        
        $class_name = ucwords($params['class'], '_');
        $class_name = str_replace('_', '\\', $class_name);

        $module = ModuleInfo::module($params['module']);

//        $class = ClassInfo::factory($class_name)->get();
//
//        $class['module'] = $module['name'];
        
        $class = new ClassApi($class_name);
        
        $this->template->sidebar = _view('api/sidebar', ['doc'=>$class]);

        return _view('api/class1', ['doc'=>$class, 'route'=>$this->request->route()]);
    }
    
    private function _get_md_file($module=NULL, $class=NULL)
    {
        if(!$module) return 'index';
        else
        {
            if($class) return $class;
            else return $module;
        }
        
        return FALSE;
    }

    protected function _method($params)
    {
        return 'метод '.$params['method'].' класса '.$params['class'].' модуля '.$params['module'];
    }
    
    
    
    protected function _module_info($module)
    {
        
    }

    protected function _get_namespace_list($paths=NULL)
    {
        if($paths === NULL)
        {
            $paths = Arr::flip(\Autoload::$_paths);
            
            $paths = Arr::map(function($a){return $a.DIRECTORY_SEPARATOR.'classes';}, $paths);
        }
        
        
        
        return $paths;
    }
    
    protected function _ns2dir($namespace)
    {
        $arr = explode('\\', $namespace);
        $module = array_shift($arr);
        
        $path = implode(DIRECTORY_SEPARATOR, $arr);
        if(!empty($path)) $path = DIRECTORY_SEPARATOR.$path;
        
        $dir = array_search($module, \Autoload::$_paths);
        $dir .= DIRECTORY_SEPARATOR.'classes'.$path;
        
        
        if(is_dir($dir)) return $dir;
        else
        {
            $dir = (string) substr($dir, strlen(SRCPATH));
            throw new Exception\Exception('directory ":dir" the corresponding namespace ":ns" not found', array(':dir'=>$dir, ':ns'=>$namespace));
        }
    }
    
    protected function _dir_info($dir)
    {
//        $dir = SRCPATH.DIRECTORY_SEPARATOR.$dir;
        $result = [];
        foreach(glob($dir.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR) AS $filepath)
        {
            $file = (string) substr($filepath, strlen($dir)+1);
            if(is_dir($filepath)) $result[$file] = $this->_dir_info($filepath);
            else $result[] = $file;
        }
        return $result;
    }

    protected function _classinfo($class_name)
    {
//        echo 'qqq'; exit;
        
        $class = array();
//        $class_name = $this->module.'\\'.$this->classname;
        $reflector = new \ReflectionClass($class_name);
        
        $class['namespace'] = $reflector->getName();
        
        return (object)$class;
    }
}
