<?php
namespace App;
/**
 * Description of Validation
 *
 * @author JackRabbit
 */
use Core\Core;
use Core\Helper\Arr;
use Core\I18n;

trait Valid
{
//    public static $class = __CLASS__;
    
    public static function test()
    {
        static::$class = __CLASS__;
//        self::$class = __CLASS__;
        
        return static::$class;
        
//        return 'lala';
    }
   
    public static function callback($callback, $params)
    {
        return NULL;
    }
    
    public static function required($value, array $rules)
    {
        $required = Arr::get($rules, 'required', FALSE);
               
        if($required !== FALSE)
        {
           $required = TRUE;
           unset($rules['required']);           
        }
        
        return ($value === '' && $required === TRUE) ? Core::message(__FUNCTION__, [':field'=>$this->field]) : NULL;
    }
    
    public static function min_lenth($str, $lenth, $is_null = FALSE)
    {    
        if(empty($lenth[0])) return NULL;
        else $lenth = $lenth[0];
        
        if(mb_strlen($str) < $lenth) return Core::message(__FUNCTION__, [':min'=>$lenth, ':field'=>$this->field]);
        else return NULL;
    }
    
    public static function max_lenth($str, $lenth)
    {
        if(empty($lenth[0])) return NULL;
        else $lenth = $lenth[0];
        
        if(mb_strlen($str) > $lenth) return Core::message(__FUNCTION__, [':max'=>$lenth]);
        else return NULL;
    }
    
    public static function range($str, array $range = [])
    {
//        echo __FUNCTION__;
        
        if(empty($range)) return NULL;
        if(!isset($range[1]))
        {
            if(mb_strlen($str) < $range[0]) return Core::message ('min_lenth', [':min'=>$range[0]]);
        }
        elseif($range[0] <= 0)
        {
            if(mb_strlen($str) > $range[1]) return Core::message ('max_lenth', [':max'=>$range[1]]);
        }
        else
        {
            if(mb_strlen($str) < $range[0] || mb_strlen($str) > $range[1]) return Core::message (__FUNCTION__, [':min'=>$range[0], ':max'=>$range[1]]);
        }
        return NULL;
    }
    
    public static function confirm($str, $arg)
    { 
        $name = $arg[0];
        $field = '"'.Arr::get($arg, 1, __('Password')).'"';
        
        return ((string)$str === (string)$this->post[$name]) ? NULL : Core::message(__FUNCTION__, [':field'=>$field]);
    }


    public static function regexp($str, $options)
    {
        $regex = $options[0];
            
        if(preg_match($regex, $str, $matches) === 0) return Core::message (__FUNCTION__, [':field'=>$this->field]);
        else return NULL;
    }
    
    public static function date($str, $options = [])
    {   
        if(empty($options)) $format = I18n::l10n(I18n::$current_lang, 'date');
        else $format = Arr::get($options, 0, 'd.m.Y');
            
        $d = \DateTime::createFromFormat($format, $str);
        if($d && $d->format($format) == $str) return NULL;
        else
        {
            $user_date_format = I18n::l10n(I18n::$current_lang, 'user_date_format');
            return Core::message (__FUNCTION__, [':format'=>$user_date_format]);
        }
    }
    
    public static function filter($var, $filter)
    {      
        $validate = array_shift($filter);
        
        $filter = Arr::get($filter, 0);
        
        $key = Arr::get($filter, 'error');
        if($key === NULL) $key = 'filter.'.$validate;
        else unset($filter['error']);
                
        $options = Arr::get($filter, 'options', []);
        $values = [];
        foreach($options AS $k=>$v)
        {
            $kk = ':'.$k;
            $values[$kk] = $v;
        }
        
        $values[':field'] = $this->field;
        
        if(!filter_var($var, $validate, $filter)) return Core::message($key, $values);
        else return NULL;
    }
}
