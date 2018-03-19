<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Helper;

/**
 * Description of Header
 *
 * @author JackRabbit
 */
class Headers
{
    public static function get($path = NULL)
    {
        $result = [];
        $r = [];
        
        foreach(headers_list() AS $header_str)
        {
            $header_0 = explode(':', $header_str, 2);
            
            $header = strtolower(Arr::get($header_0, 0));
            $key_val_str = trim(Arr::get($header_0, 1, ''));
            $key_val_arr = explode(';', $key_val_str);
            
            $h = [];
            foreach($key_val_arr AS $key_val)
            {
                $pair = explode('=', trim($key_val));
                
                $key = strtolower(Arr::get($pair, 0));
                $value = Arr::get($pair, 1);
                
                $h[$key] = $value;
            }
            
//            var_dump($h);
            
//            $r[$header] = $h;
//            
//            if($path)
//            {
//                $result[] = Arr::path($r, strtolower($path));                
//            }            
//            elseif($r)
            if($path === NULL)
            {
                $result[][$header] = $h; //$r;
            }
            else
            {
                $r[$header] = $h;
                
                var_dump($r);
                
                $re = Arr::path($r, strtolower($path));
                
//                var_dump($re);
                
                if($re) $result[] = $re;
            }
            
        }
        
        return (count($result) === 1) ? $result[0] : $result;
    }
    
    public static function set($header, $key_val)
    {
        $header = rtrim(ucwords($header, '-'), '-').':';
        
        if(is_array($key_val))
        {
            foreach($key_val AS $key=>$val)
            {
                $header .= ' '.rtrim(ucwords($key. '-'), '-').'='.$val;
            }
        }
        else $header .= ' '.$key_val;
        
//        return $header;
        
        header($header);
    }
    
    public static function is_sent($path, $value = NULL)
    {
//        $path = 'set-cookie.'.$key;
        $header = Headers::get($path);
        
        if($value === NULL)
        {
            return ($header) ? TRUE : FALSE;
        }
        else
        {
            if(is_array($header))
            {
                return (in_array($value, $header)) ? TRUE : FALSE;
            }
            else return ($header == $value) ? TRUE : FALSE;
        }        
    }
}
