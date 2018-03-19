<?php
namespace Core\Cache;

/**
 * Description of Client
 *
 * @author JackRabbit
 */
use Core\Helper\Arr;

class Client
{
//    protected $_cache = TRUE;
//    
//    protected $_max_age = 60;
//    
//    protected $etag = TRUE;
    
    protected $vars = array(
        'cache'     => TRUE,
        'max_age'   => 60,
        'etag'      => TRUE,
    );
    
    public function get($key = NULL)
    {
        if($key === NULL) return $this->vars;
        else return Arr::get($this->vars, $key);
    }
    
    public function set($arg = array())
    {
        if(func_num_args() === 1 && is_array($arg))
        {
            $this->vars = array_replace($this->vars, $arg);
        }
        elseif(func_num_args() === 2)
        {
            list($key, $value) = func_get_args();
            $this->vars[$key] = $value;
        }
        
        return $this;
    }
}
