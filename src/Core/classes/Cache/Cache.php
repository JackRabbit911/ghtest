<?php

namespace Core\Cache;

/**
 * list of methods to cache
 * 
 * @author JackRabbit
 */
interface Cache
{
    public function get($key);
    
    public function set($key, $value, $lifetime);
    
    public function delete($key = NULL);
}
