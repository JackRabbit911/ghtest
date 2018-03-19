<?php
namespace User;
/**
 * Description of Token
 *
 * @author JackRabbit
 */

use Core\HTTP;
use Core\Helper\Text;

class Token
{
    public static $cookie_name = 'WNT';
    public static $cookie_path = '/';
    public static $lifetime = 3600;
    
//    public static $table_name = 'tokens';
    public static $save_handler = 'files';
    
    
    protected static $instance;
    
    protected $_driver;
    
    public static function instance($driver = NULL)
    {        
        if(!(static::$instance instanceof static))
        {
            static::$instance = new static($driver);
        }
        return static::$instance;
    }
    
    protected function __construct($driver)
    {
       if($driver === NULL) $driver = static::$save_handler;
       $driver =  __NAMESPACE__.'\Model\Token\\'.ucfirst($driver);
       $this->_driver = new $driver();
    }
    
    public function user_id()
    {
        $id = $this->get('user_id');
        $this->set($id);
        return $id;
    }

    public function get($key = NULL)
    {
        $token = filter_input(INPUT_COOKIE, static::$cookie_name, FILTER_SANITIZE_SPECIAL_CHARS);
        
        if($token)
        {            
            $data = $this->_driver->get($token);
                        
            if(isset($data->user_agent) && $data->user_agent !== HTTP::user_agent())
            {
                $this->delete($token);
                return NULL;
            }            
            
            if($key !== NULL && isset($data->$key))
                return $data->$key;
            else return $data;
            
        }
        else return NULL;
    }
    
    public function set($user_id = FALSE)
    {
        if(empty($user_id)) return;
        
        $token = filter_input(INPUT_COOKIE, static::$cookie_name, FILTER_SANITIZE_SPECIAL_CHARS);
        
        $new_token = $this->create();
                
        $data = [
            'token'=>$new_token, 
            'user_id'=>$user_id, 
            'user_agent'=>HTTP::user_agent(), 
            'last_activity'=>time()
            ];
               
        $this->_driver->set($data, $token);
        
        setcookie(static::$cookie_name, $new_token, time()+static::$lifetime, static::$cookie_path);
        
        return $new_token;
    }
    
    public function create()
    {
        do
        {
            $token = sha1(uniqid(Text::random('alnum', 32), TRUE));
        }
        while(!$this->_driver->is_unique($token));
        
        return $token;
    }

    public function delete($token = NULL)
    {
        if($token === NULL)
            $token = filter_input(INPUT_COOKIE, static::$cookie_name, FILTER_SANITIZE_SPECIAL_CHARS);
        
        if($token)
            $this->_driver->delete($token);
        
        setcookie(static::$cookie_name, FALSE, -100, static::$cookie_path);
    }
    
    public function gc($lifetime = NULL)
    {
        if($lifetime === NULL) $lifetime = static::$lifetime;
        return $this->_driver->gc($lifetime);
    }
    
}
