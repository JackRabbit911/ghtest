<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Docs\Controller;

use Core\Core;
//use Core\Cache;
use Core\Controller;
//use Core\Exception;
//use Core\Helper\Arr;
//use Core\Helper\Text;
//use Docs\Parsedown;
//use Docs\ClassInfo;
/**
 * Description of Index
 *
 * @author JackRabbit
 */
class Index extends Controller\Template
{
//    public $template = $this->module.'/views/template';
    
    protected function _before()
    {
        $this->template = $this->modpath.'views/template';
        
        parent::_before();
        $menu = Core::config($this->modpath.'config/menu');
        
        $this->template->header = $this->_view($this->modpath.'views/header');
        
        $this->template->navbar = $this->_view($this->modpath.'views/navbar', $menu)
                ->set('params', $this->request->params())
                ->set('prefix', BASEDIR.'/docs');
        
        $this->template->sidebar = '';
    }
    
    public function index()
    {
        $this->response->headers('cache-control', 'max-age=30');
        $this->template->title = 'Webnigger Guide';
        $this->template->content = $this->module;
    }
    
    public function framework()
    {       
        $this->template->title = 'Webnigger framework guide';
        $this->template->content = 'framework';
    }
    
    public function cmf()
    {       
        $this->template->title = 'Webnigger CMF guide';
        $this->template->content = 'cmf';
    }
    
    public function cms()
    {       
        $this->template->title = 'Webnigger CMS guide';
        $this->template->content = 'cms';
    }
    
    public function sitebuilder()
    {       
        $this->template->title = 'Webnigger Site Builder guide';
        $this->template->content = 'SiteBuilder';
    }
    
}

