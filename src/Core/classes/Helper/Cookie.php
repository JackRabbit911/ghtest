<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Helper;

/**
 * Description of Cookie
 *
 * @author JackRabbit
 */


class Cookie
{
    public static $cookies = [];
    
    public static function get($key)
    {
        return filter_input(INPUT_COOKIE, $key, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    
    public static function set($key, $value, $lifetime = 0, $path = "/")
    {
        setcookie($key, $value, $lifetime, $path);
    }
    
    public static function delete($key)
    {
//        $header = Headers::get('set-cookie');
        
//        $domain = '/';
//        
//        if(is_array($header))
//        {
//            foreach($header AS $item)
//            {
//                if(stripos($item, $key) !== FALSE)
//                {
//                    $sub = stristr($item, 'path=');
//                    if($sub) $domain = substr ($sub, 5);
//                }                
//            }
//        }
//        else
//        {
//            if(stripos($header, $key) !== FALSE)
//            {
//                $sub = stristr($header, 'path=');
//                if($sub) $domain = substr ($sub, 5);
//            }    
//        }
        
        setcookie($key, FALSE);
    }
    
    public static function is_sent($key, $value = NULL)
    {
//        $path = 'set-cookie.'.$key;        
        return Headers::is_sent('set-cookie', $key.'='.$value);
    }
    
    
}
