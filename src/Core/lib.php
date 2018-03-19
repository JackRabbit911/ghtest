<?php
use Core\I18n as I18n;
use Core\View;

function __($string, array $values = NULL, $lang = 'en-us')
{
    return I18n::gettext($string, $values);
}

function _f($key, $value)
{    
    $default = [
        'date'  => 'd.m.Y',
        'time'  => 'H:i',
        'float' => '2',
        'currency'  => 'USD',
    ];

    $format = I18n::l10n(I18n::$current_lang, $key);

    if($format === NULL && isset($default[$key])) $format = $default[$key];

    if(function_exists($key) && $format)
    {
        return call_user_func_array($key, array($format, $value));
    }
    elseif(method_exists('Core\I18n', $key))
    {
        return call_user_func_array(array('Core\I18n', $key), array($format, $value));
    }
    else
    {
        return $value; // float($format, $value);
    }
    
}

function _view($file, array $data = NULL)
{
    return Core\View::factory($file, $data);
}

function _js($str=NULL)
{
    if($str === NULL)
    {
        $result = NULL;
        if(is_array(View::$js) && !empty(View::$js))
        {
            foreach(View::$js AS $str)
            {
                $str = '/'.ltrim($str, '/');
                $result .= '<script src="'.BASEDIR.$str.'"></script> '.PHP_EOL;
            }
        }
        return $result;
    }
    else
    {
        $str = '/'.ltrim($str, '/');
        
        if(!in_array($str, View::$js))
        {               
            View::$js[] = $str;
            return '<script src="'.$str.'"></script>';
        }
        else return NULL;
    }
}


