<?php
namespace Core;

/**
 * Description of Session
 *
 * @author JackRabbit
 */
use Core\Core;
use Core\Helper\Arr;
use Core\Helper\Cookie;
//use Core\Session\Sqlite;


class Session
{
    
    use \Core\Pattern\Singletone;
    
    /**
     * TRUE - session start for users, wich already has session cookie
     * FALSE - session start for all visitors
     * 
     * @var boolean
     */
    protected static $settings = [];




//    protected static $users_only = TRUE;
//    
//    public static $save_path = SRCPATH.'App'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.'sessions';
//    
//    protected static $save_handler = 'files';
//    
//    public static $cookie_path = '/';
//    
//    public static $handler;
//
//    protected static $lifetime = 0;  //Cookie lifetime
//    
//    protected static $gc_time = 600; // 7200;
//    
//    public static $name = 'WNSID';
    
//    protected static $ = NULL;
    
    
    protected $session_id;
    
    public static function settings(array $settings = [])
    {
//        $config = Core::config('sessions');
//        if($config === NULL) $config = [];
        static::$settings = Arr::merge(static::$settings, $settings);
            
//        var_dump(static::$settings);
    }
    
    public function __construct()
    {
        $config = Core::config('sessions');
        if($config === NULL) $config = [];
        static::$settings = Arr::merge($config, static::$settings);
        
        $this->session_id = Cookie::get(static::$settings['cookie_name']);
        
//        if($this->session_id !== NULL)
    }
    
    public function start()
    {
        $this->regenerate();
        
        return $this;
    }
    
    public function id()
    {
        return $this->session_id;
    }
    
    public static function mkdir()
    {
        if(!is_dir(static::$save_path))
        {
            mkdir(static::$save_path, 0755);
        }

        if(!is_writable(static::$save_path))
        {
            chmod(static::$save_path, 0755);
        }                   
    }
    
    protected static function _handler()
    {
        $handler = '\Core\Session\\'.ucfirst(static::$save_handler); 
        
        if(static::$save_handler === 'files')
        {
            static::$save_path = rtrim(static::$save_path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        }
        elseif(static::$save_handler === 'memcache')
        {
            $connection = (object)Core::config('connect', 'memcache');           
            static::$save_path = 'tcp://'.$connection->server.':'.$connection->port;
            $handler = '\Memcache';
        }
        elseif(in_array(static::$save_handler, ['sqlite', 'mysql']))
        {
            static::$save_handler = 'user';
        }
        elseif(class_exists(static::$save_handler))
        {        
            $handler = ucfirst(static::$save_handler);            
            static::$save_handler = 'user';
        }
        else{}
        
        static::$handler = new $handler();
    }
    
//    public function start()
//    {
//         if(session_status() < PHP_SESSION_ACTIVE)
//         {
//             session_start();
////             return TRUE;
//         }
////         else return FALSE; // 'status '.session_status();
//         return $this;
//    }
    
    public function set($key, $value)
    {
        if(session_status() < 2) return;
        $_SESSION[$key] = $value;
    }
    
    public static function get($key = NULL, $default = FALSE)
    {
        
        
        if(!isset($_SESSION)) return $default;
        
        if($key === NULL) return $_SESSION;
        else return Arr::get($_SESSION, $key, $default);
    }
    
    public function delete($key)
    {
        if($key === NULL) $_SESSION = [];
        elseif(isset($_SESSION[$key])) unset($_SESSION[$key]);
    }
    
    public function flush($key = NULL, $default = FALSE)
    {
        $result = $this->get($key, $default);
        $this->delete($key);
        return $result;
    }
    
//    public function id()
//    {
//        return Arr::get($_COOKIE, static::$name);
////        return session_id();
//    }
    
    public function last_activity()
    {
//        if(file_exists($this->file)) return filemtime($this->file);
//        else return FALSE;
        
        return static::$handler->last_activity();
    }
    
    public function destroy()
    {
        $sid = session_id();
        
        
        
        $old_sid = Arr::get($_COOKIE, static::$name);
        
        
        
        
        
//        return $sid;
        
        if(session_status() === PHP_SESSION_ACTIVE)
            session_destroy();
        
        
        static::$handler->destroy($sid);
        static::$handler->destroy($old_sid);
        
//        var_dump(headers_list());
        
//        $header = 'Set-Cookie: WNSID=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=0; path=/';        
//        if(!in_array($header, headers_list()));
//        {           
//            setcookie(static::$name, "", -100, '/');
//        }
        
        if(!Cookie::is_sent(session_name(), 'deleted'))
        {
            Cookie::delete(session_name());
        }
        
//        var_dump(headers_list());
        
        static::$instance = NULL;
        
//        echo $sid.'<br>';
//        echo $old_sid.'<br>';
//        exit;
        
    }
    
    public static function gc($lifetime=NULL)
    {
        if(static::$handler === NULL) static::_handler();
        if(!$lifetime) $lifetime = static::$gc_time;
        return static::$handler->gc($lifetime);
    }
    
    protected function security()
    {
        if($this->get('user_id')) return TRUE;
        else return FALSE;
    }
    
//    public function regenerate($strict = NULL)
//    {
//        if($strict === NULL) $strict = static::$strict;
//        
////        var_dump($strict); exit;
//        if(method_exists(static::$handler, 'regenerate'))
//            static::$handler->regenerate($strict);
//        else session_regenerate_id();
//    }
    
    protected function check($check = TRUE)
    {   
//        return FALSE;
        
        if($check === FALSE) return TRUE;
        
        if(!isset($_SESSION['user_agent']) && !isset($_SESSION['client_ip']))
        {
            $this->set('client_ip', HTTP::client_ip());
            $this->set('user_agent', HTTP::user_agent());
//            $_SESSION['user_ip'] = HTTP::client_ip();
//            $_SESSION['user_agent'] = HTTP::user_agent();
            return TRUE;
        }        
        elseif($_SESSION['client_ip'] === HTTP::client_ip() && $_SESSION['user_agent'] === HTTP::user_agent())
            return TRUE;
        else return FALSE;
    }
}
