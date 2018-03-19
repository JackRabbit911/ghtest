<?php
namespace Core;

/**
 * Output Cache.
 *
 * @author JackRabbit
 */

use Core\Core;
use Core\Helper\Arr;

class Cache
{    
    const FILE = 'File';
    const MEMCACHE = 'Memcache';
    
   
    
    /**
     * @var  boolean  server output cache YES/NO
     */
    public static $output = FALSE;
    
    /**
     * @var string enjine of cache method
     */
    protected static $_enjine = self::FILE;
    
    /**
     * LifeTime for cache output default.
     * if $_lifetime === FALSE, output cache is disabled
     * 
     * @var numeric|boolean
     */
    protected static $_lifetime = 60;
    
    
//    public static $_client = array(
//        'enable'    => TRUE,
//        'max_age'   => 60,
//        'etag'      => NULL,
//        'query'     => TRUE,
//    );
    
//    protected static $_server = array(
//        'enable'    => TRUE,
//        'lifetime'  => 60,
//        'enjine'    => self::FILE,
//        'query'     => FALSE,
//    );
    
    /**
     *
     * @var numeric compression ratio (if NULL - no compress)
     */
    public static $compress = 9;
    
    /**
     * @var object instance of current enjine class
     */
    protected $_class;
    
    /**
     * @var string namespase for the enjine class
     */
    protected $_namespase = '\Core\Cache\\';
        
    /**
     * Factory pattern to create object of the enjine class
     * 
     * @param string $enjine
     * @return \static object
     */
    public static function factory($enjine = NULL)
    {
        if($enjine === NULL) $enjine = static::$_enjine;
        return new static($enjine);
    }
    
    /**
     * Set and Get lifetime variable in seconds
     * if param $lifetime === NULL - no cache
     * 
     * @param integer|boolean $lifetime
     * @return integer|void
     */
    public static function lifetime($lifetime = NULL)
    {
        if($lifetime !== NULL)
            static::$_lifetime = $lifetime;
        else return static::$_lifetime;
    }
    
    /**
     * @param string $enjine
     */
    public function __construct($enjine)
    {
        $class_name = $this->_namespase.ucfirst($enjine);
        $this->_class = new $class_name();
    }

    /**
     * Get from cache if is_file and act actual
     * 
     * @param string $key
     * @return string|boolean
     * @uses Core\HTTP::$uri
     */
    public function get($key = NULL)
    {
        if(Core::$cache === TRUE)
        {
            if($key === NULL) $key = md5(HTTP::$uri);
            
            $str = $this->_class->get($key);
            
            if(empty($str)) return FALSE;
            
            $arr = explode('\r\n', $str, 2);            
            unset($str);
            
            if(count($arr) == 2 && $headers = unserialize($arr[0])) HTTP::header($headers);
            
            $output = array_pop($arr);
            unset($arr);
            
            if(HTTP::match_encoding_header('deflate') && !empty(Core::$compress))
            {
                header("Content-Encoding: deflate", FALSE);
            }
            else $output = $this->uncompress($output);
            
            return $output;
        }
        else return FALSE;
    }
    
    /** 
     * @param string $str
     * @return string
     */
    public function compress($str)
    {
        if(empty(static::$compress)) return $str;
        else return gzdeflate($str, static::$compress);
    }
    
    /** 
     * @param string $str
     * @return string
     */
    protected function uncompress($str)
    {
        if(static::$compress === FALSE) return $str;
        else return gzinflate($str);
    }
    
    /**
     * Set output string into cache
     * 
     * @param string $key 
     * @param string $value
     * @param integer $lifetime
     * @return void
     */
    public function set($key, $value, $lifetime=NULL)
    {
        if(static::$output === FALSE) return;
        
        if($lifetime === NULL) $lifetime = static::$_lifetime;
        
        if($lifetime === FALSE) return;
        
        if($headers = apache_response_headers())
            $headers = serialize(apache_response_headers()).'\r\n';
        else $headers = NULL;
        
        $value = $headers.$this->compress($value);
//        
        $this->_class->set($key, $value, $lifetime);
//        
//        return $etag;
    }
    
    /**
     * Delete cache.  
     * if $key === '*' delete all cache records (files)   
     * if $key === NULL delete all cache records or files whose lifetime has expired
     * 
     * @param string $key
     */
    public function delete($key=NULL)
    {
        $this->_class->delete($key);
    }
    
    
//    public static function client()
//    {
//        $num = func_num_args();
//        
//        if($num === 0) return self::$_client;
//        elseif($num === 1) 
//        {
//            $arg = func_get_arg(0);
//            if(is_array($arg)) self::$_client = array_replace(self::$_client, $arg);
//            else return Arr::get(self::$_client, $arg);
//        }
//        elseif($num === 2)
//        {
//            list($key, $value) = func_get_args();
//            self::$_client[$key] = $value;
//        }
//    }
    
//    public static function server()
//    {
//        $num = func_num_args();
//        
//        if($num === 0) return self::$_server;
//        elseif($num === 1) 
//        {
//            $arg = func_get_arg(0);
//            if(is_array($arg)) self::$_server = array_replace(self::$_server, $arg);
//            else return Arr::get(self::$_server, $arg);
//        }
//        elseif($num === 2)
//        {
//            list($key, $value) = func_get_args();
//            self::$_server[$key] = $value;
//        }
//    }
   
    
}
