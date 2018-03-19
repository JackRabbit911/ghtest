<?php

namespace Core\Cache;
/**
 * File enjine for read, write, delete cache
 *
 * @author JackRabbit
 */

use Core\Cache\Cache;

class File implements Cache
{
    /**
     * Fullpath to cache directory
     */
    const CACHEPATH = SRCPATH.'App'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR;
    
    /**
     * Garbage collection ON/OFF
     * 
     * @var boolean
     */
    protected $_gc = FALSE;
    
    /**
     * Create cache directory if not exist
     */
    public function __construct($gc = TRUE)
    {
        if(!is_dir(self::CACHEPATH))
        {
            mkdir(self::CACHEPATH, 0755);
        }
        
        if(!is_writable(self::CACHEPATH))
        {
            chmod(self::CACHEPATH, 0755);
        }
        
        $this->_gc = $gc;
    }

    /**
     * Get content from file  
     * filename like **lifetime-hashkey.txt**  
     * detect lifetime from filename.  
     * if lifetime has expired, unlink file and return FALSE
     * 
     * @param string $key
     * @return string|boolean
     */
    public function get($key)
    {
        $now = time();
        foreach(glob(self::CACHEPATH.'*-'.$key.'.txt') AS $file)
        {
            $lifetime = (int)basename($file, "-$key.txt");
            
            if($lifetime === 0) $endtime = $now + 1;
            else $endtime = $lifetime + filemtime($file);
            if($endtime < $now) unlink($file);
        }
        
        return (isset($file) && file_exists($file)) ? file_get_contents($file) : FALSE;
    }
    
    /**
     * Set content string into the file.  
     * if it`s needed create file with filename **lifetime-hashkey.txt**  
     * if $_gc is TRUE delete all outdated files
     * 
     * @param string $key
     * @param string $value
     * @param integer $lifetime
     * @return void
     */
    public function set($key, $value, $lifetime)
    {
        if($this->_gc === TRUE) $this->delete();
        
        $cache_file = self::CACHEPATH.$lifetime.'-'.$key.'.txt';
        file_put_contents($cache_file, $value);
        
//        $etag = md5($key.filemtime($cache_file));
//        
//        return $etag;
    }
    
    /**
     * Delete file from cache directory  
     * if $key === '*' - delete all files  
     * if $key === NULL - delete all outdated files
     * 
     * @param string $key
     */
    public function delete($key = NULL)
    {
        if($key !== NULL)
        {
            foreach(glob(self::CACHEPATH.'*-'.$key.'.txt') AS $file) unlink($file);
        }
        else
        {
            if (file_exists(self::CACHEPATH))
            {    
                $now = time();
                foreach (glob(self::CACHEPATH.'*') as $file)
                {
                    $lifetime = (int)basename($file, "-$key.txt");
                    if($lifetime === 0) $endtime = $now + 1;
                    else $endtime = $lifetime + filemtime($file);
                    if($endtime < $now) unlink($file);
                }
            }
        }
    }
    
}
