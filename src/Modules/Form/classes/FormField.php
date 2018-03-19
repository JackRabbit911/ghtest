<?php
namespace Form;
/**
 * Description of Form
 *
 * @author JackRabbit
 */

use Core\Helper\Arr;

trait FormField
{
    public $css = array();
    public $js = array();
    
    public $validation;
    
    protected $view_path = 'default/';
    
    public static function factory($name, $view_name=NULL)
    {
        return new static($name, $view_name);
    }
    
//    public function getAttr($key = NULL)
//    {
//        return ($key) ? Arr::path($this->attr, $key) :  $this->attr;
//    }
//    
//    public function addAttr($key, $value)
//    {
//        if(isset($this->attr[$key]) && is_array($this->attr[$key]))
//        {
//            if(!is_array($value)) $value = [$value];
//            $this->attr[$key] = Arr::merge($this->attr[$key], $value);
//        }
//        else $this->attr[$key] = $value;
//        
//        return $this;
//    }
//    
//    public function removeAttr($key, $value = NULL)
//    {
//        if(isset($this->attr[$key]))
//        {
//            if($value === NULL) unset($this->attr[$key]);
//            else
//            {
//                if(!is_array($value)) $value = [$value];
//
//                foreach($value AS $val)
//                {
//                    $k = array_search($val, $this->attr[$key]);
//                    if($k) unset($this->attr[$key][$k]);
//                }
//            }
//        }
//        
//        return $this;
//    }
    
    public function attr($key, $value = FALSE)
    {
        if($value === FALSE)
            return Arr::get($this->attr, $key, FALSE);
        else
        {
            $this->attr[$key] = $value;
            return $this;
        }
    }

    public function js($path)
    {
        $this->js[] = $path;
        
        return $this;
    }
    
    public function css($path)
    {
        $this->css[] = $path;
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->render();
    }
}
