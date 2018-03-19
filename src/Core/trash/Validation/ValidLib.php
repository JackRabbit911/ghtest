<?php
namespace Core\Validation;
/**
 * Description of Validation
 *
 * @author JackRabbit
 */
use Core\Helper\Arr;
use Core\I18n;

class ValidLib
{
    public static function required($value)
    {
        return ($value === '') ? FALSE : TRUE;
    }
    
    public static function min_lenth($str, $lenth)
    {    
        if(mb_strlen($str) < $lenth)
        {
//            $this->response[$this->name_field]['replace'] = [':min'=>$lenth];
            return FALSE;
        }
        else return TRUE;
    }
    
    public static function max_lenth($str, $lenth)
    {
        if(mb_strlen($str) > $lenth)
        {
//            $this->response[$this->name_field]['replace'] = [':max'=>$lenth];
            return FALSE;
        }
        else return TRUE;
    }
    
    public static function lenth($str, $min, $max)
    {
        if(mb_strlen($str) < $min || mb_strlen($str) > $max)
        {
//            $this->response[$this->name_field]['replace'] = [':min'=>$min, ':max'=>$max];
            return FALSE;
        }
        else return NULL;
    }
    
    public static function confirm($validation, $value, $name_to_confirm = 'password')
    { 
        $confirm = $validation->response[$name_to_confirm]['value'];
        
        
        if((string)$value === (string)$confirm) return TRUE;
        else
        {
//            $this->response[$this->name_field]['replace'][':field'] = $label;
            return FALSE;
        }
        
    }
    
    public static function regexp($value, $regex)
    {  
        return (preg_match($regex, $value) === 0) ? FALSE : TRUE;
    }
    
    public static function check_date($str, $format = NULL)
    {   
        if($format === NULL) $format = I18n::l10n(I18n::$current_lang, 'date');
            
        $d = \DateTime::createFromFormat($format, $str);
        if($d && $d->format($format) == $str) return TRUE;
        else
        {
//            $user_date_format = I18n::l10n(I18n::$current_lang, 'user_date_format');
//            $this->response[$this->name_field]['code'] = 'date';
//            $this->response[$this->name_field]['replace'][':format'] = $user_date_format;
            
            return FALSE;
        }
    }
    
    public static function filter($value, $filter = FILTER_DEFAULT, $options = [])
    {             
        if(!filter_var($value, $filter, $options))
        {
//            $this->response[$this->name_field]['code'] = 'filter.'.$filter;
            
//            if(!empty($options))
//            {
//                $this->response[$this->name_field]['code'] .= '-options';
//                $this->response[$this->name_field]['replace'] = Arr::get($options, 'options', []);
//            }
            
            return FALSE;
        }
        else return TRUE;
    }
}
