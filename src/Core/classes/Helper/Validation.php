<?php
namespace Core\Helper;
/**
 * Description of Validation
 *
 * @author JackRabbit
 */
//use Core\Helper\Arr;
use Core\I18n;

class Validation
{
    public static function required($value)
    {
        return ($value === '') ? FALSE : TRUE;
    }
    
    public static function min_lenth($value, $min)
    {    
        if(mb_strlen($value) < $min)
        {
//            $this->response[$this->name_field]['replace'] = [':min'=>$lenth];
            return FALSE;
        }
        else return TRUE;
    }
    
    public static function max_lenth($value, $max)
    {
        if(mb_strlen($value) > $max)
        {
//            $this->response[$this->name_field]['replace'] = [':max'=>$lenth];
            return FALSE;
        }
        else return TRUE;
    }
    
    public static function lenth($value, $min, $max)
    {
        if(mb_strlen($value) < $min || mb_strlen($value) > $max)
        {
//            $this->response[$this->name_field]['replace'] = [':min'=>$min, ':max'=>$max];
            return FALSE;
        }
        else return NULL;
    }
    
    public static function confirm($value, $name_to_confirm = 'password', $_validation = ':valid', $name = ':name')
    { 
        $_validation->replace['confirm'][':field'] = __(ucfirst($name_to_confirm));
        
        if(!isset($_validation->response[$name_to_confirm]['value']))
            return FALSE;
        else
            $confirm = $_validation->response[$name_to_confirm]['value'];
        
//        echo $value.' '.$confirm; exit;
        
        if((string)$value === (string)$confirm) return TRUE;
        else
        {
            return FALSE;
        }
        
    }
    
    public static function regexp($value, $regex)
    {  
        return (preg_match($regex, $value) === 0) ? FALSE : TRUE;
    }
    
    public static function check_date($value, $_validation = ':valid', $name = 'date', $format = NULL)
    {   
        if($format === NULL) $format = I18n::l10n(I18n::$current_lang, 'date');
            
        $d = \DateTime::createFromFormat($format, $value);
        if($d && $d->format($format) == $value) return TRUE;
        else
        {
            $user_date_format = I18n::l10n(I18n::$current_lang, 'user_date_format');
            $_validation->response[$name]['code'] = 'date';
            $_validation->replace[$name][':format'] = (string)$user_date_format;
            return FALSE;
        }
    }
    
    public static function filter($value, $filter = FILTER_DEFAULT, $options = [], $validation = ':valid', $name = ':name')
    {             
        if(!filter_var($value, $filter, $options))
        {
            $validation->response[$name]['code'] = 'filter.'.$filter;
            
            if(!empty($options))
            {
                $validation->response[$name]['code'] .= '-options';
//                $this->replace[$name] = Arr::get($options, 'options', []);
            }
            
            return FALSE;
        }
        else return TRUE;
    }
}
