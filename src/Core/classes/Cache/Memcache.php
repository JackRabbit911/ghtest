<?php

namespace Core\Cache;
/**
 * Memcache enjine
 *
 * @author JackRabbit
 */
use Core\Core;
use Core\Cache\Cache;
use Core\Exception\Exception;

class Memcache implements Cache
{
    /**
     * @var object instance of the Memcache class
     */
    protected $_memcache;
    
    /**
     * Connect to Memcache  
     * Create object of Memcache class  
     * :KLUDGE: Не отлавливается ошибка подключения
     * 
     * @uses Core\Config
     * $throws Core\Exception\Exception
     */
    public function __construct($server = NULL, $port = 11211)
    {
        if($server === NULL)
        {
            $connection = (object)Core::config('connect', 'memcache');
            if(!$connection)
            {
                $connection->server = 'localhost';
                $connection->port = $port;
            }
        }
        else
        {
            $connection = new \stdClass();
            $connection->server = $server;
            $connection->port = $port;
        }
                
        $this->_memcache = new \Memcache;
        
        
        // здесь косяк: ошибка подключения не отлавливается
        try
        {
            $this->_memcache->connect($connection->server, $connection->port);
        }        
        catch(Exception $e)
        {
            if(Core::$errors === TRUE)
                throw new Exception('Memcache connect is`t success. Server ":server", port ":port"', 
                        array(':server'=>$connection->server, ':port'=>$connection->port));
        }
    }
    
    /**
     * Get content from Memcache record
     * 
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        return $this->_memcache->get($key);
    }
    
    /**
     * Set content to the Memcache record
     * 
     * @param string $key
     * @param string $value
     * @param integer $lifetime
     */
    public function set($key, $value, $lifetime)
    {
        $this->_memcache->set($key, $value, MEMCACHE_COMPRESSED, $lifetime);
        
        $etag = md5($key.mktime());
        
        return $etag;
    }
    
    /**
     * Delete Memcache record.  
     * if $key === NULL - flush all outdated records
     * 
     * @param string|boolean $key
     */
    public function delete($key = NULL)
    {
        if($key === NULL || $key === '*') $this->_memcache->flush();
        else $this->_memcache->delete($key);
    }
    
}
