<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

use Core\Controller;
use Core\Route;
use Core\Core;
use Core\Exception\Exception;
use Core\HTTP;
use Core\Cache;
use Core\Helper\File;
use Core\Session;
use Core\Helper\Arr;
use Core\DB;
use Core\DB\Select;
/**
 * Description of Index
 *
 * @author JackRabbit
 */
class Index extends Controller\Controller
{   
    public function index()
    {   
        
    }
    
    public function lala()
    {
//        Core::$cache = FALSE;
        Cache::$output = FALSE;
//        $this->response->headers('cache-control', 'max-age=0');
//        $this->response->headers('etag', Cache::client('etag'));
//        if(!$this->_cache_control(0, Cache::client('etag')))
            echo '<p id="intro-dm">ПРЕВЕД!</p>';
            $this->response->headers('cache-control', 'max-age=30');
            
//            var_dump(Cache::client('etag'));
        
////        $str = $this->request->accept->headers('Accept-Language');
////        $res = $this->request->accept->parse_language_header($str);
////        $res = $this->request->accept->language_at_quality('de');
////        $res = $this->request->accept->preferred_language(['en-us', 'en-gb', 'ru-al', 'ru-ru', 'ru-ua'], TRUE);
//        
//        $langs = ['ru-ru', 'ru', 'en', 'es'];
//        
////        $res = $this->request->accept->headers();
//        
////        $res = $this->request->accept->type("text/html");
//        
//        $res = $this->request->headers->cache_control('host');
//        
//        $res = HTTP::detect_url();
//        
////        $res = $_SERVER;
//        
//        var_dump($res);
    }
    
    public function _qq()
    {
        echo 'YCE OK';
    }
}
