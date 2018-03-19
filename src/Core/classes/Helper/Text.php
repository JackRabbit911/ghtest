<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Helper;

/**
 * Description of Text
 *
 * @author JackRabbit
 */

use Core\Core;

class Text
{
    public static $markdown = NULL;
    
    
    
    public static function markdown($string, $dir=FALSE, $tag = FALSE)
    {
        if(!class_exists('\Parsedown'))
        {
            include_once SRCPATH.'/Core/vendor/Parsedown/Parsedown.php';            
        }
        
        if(self::$markdown === NULL)
        {
            self::$markdown = new \Parsedown();
        }
        
        if($dir === FALSE)
            $result = self::$markdown->text($string);
        else
        {
            if($file = Core::find_file($string, $dir, 'md'))
            {
                $result = self::$markdown->text(file_get_contents($file));
            }
            else return NULL;
        }
        
        if($tag !== FALSE)
        {
            $result = static::htmlspecialchars($result, $tag);
        }
        
        return $result;
    }
    
    /**
     * Generates a random string of a given type and length.
     *
     *
     *     $str = Text::random(); // 8 character random string
     *
     * The following types are supported:
     *
     * alnum
     * :  Upper and lower case a-z, 0-9 (default)
     *
     * alpha
     * :  Upper and lower case a-z
     *
     * hexdec
     * :  Hexadecimal characters a-f, 0-9
     *
     * distinct
     * :  Uppercase characters and numbers that cannot be confused
     *
     * You can also create a custom type by providing the "pool" of characters
     * as the type.
     *
     * @param   string  $type   a type of pool, or a string of characters to use as the pool
     * @param   integer $length length of string to return
     * @return  string
     * @uses    UTF8::split
     */
    public static function random($type = NULL, $length = 8)
    {
            if ($type === NULL)
            {
                    // Default is to generate an alphanumeric string
                    $type = 'alnum';
            }

            $utf8 = FALSE;

            switch ($type)
            {
                    case 'alnum':
                            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    break;
                    case 'alpha':
                            $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    break;
                    case 'hexdec':
                            $pool = '0123456789abcdef';
                    break;
                    case 'numeric':
                            $pool = '0123456789';
                    break;
                    case 'nozero':
                            $pool = '123456789';
                    break;
                    case 'distinct':
                            $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
                    break;
                    default:
                            $pool = (string) $type;
                            $utf8 = ! UTF8::is_ascii($pool);
                    break;
            }

            // Split the pool into an array of characters
            $pool = ($utf8 === TRUE) ? UTF8::str_split($pool, 1) : str_split($pool, 1);

            // Largest pool key
            $max = count($pool) - 1;

            $str = '';
            for ($i = 0; $i < $length; $i++)
            {
                    // Select a random character from the pool and add it to the string
                    $str .= $pool[mt_rand(0, $max)];
            }

            // Make sure alnum strings contain at least one letter and one digit
            if ($type === 'alnum' AND $length > 1)
            {
                    if (ctype_alpha($str))
                    {
                            // Add a random digit
                            $str[mt_rand(0, $length - 1)] = chr(mt_rand(48, 57));
                    }
                    elseif (ctype_digit($str))
                    {
                            // Add a random letter
                            $str[mt_rand(0, $length - 1)] = chr(mt_rand(65, 90));
                    }
            }

            return $str;
    }
   
    
    public static function htmlspecialchars($str, $tag)
    {
        $regex = '|(<'.$tag.'>)(.*)(</'.$tag.'>)|isU';
        
        return preg_replace_callback($regex, function($m){
            $m[2] = htmlspecialchars($m[2]);
            return $m[1].$m[2].$m[3];
        }, $str);
    }
    
}
