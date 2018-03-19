<?php
namespace Core;

trait Multitone
{
    /** 
     * array of Singletones Object of real class 
     * @var  object
     */
    protected static $instance = [];
    
    /**
     * Multitone pattern
     * @return object this class
     */
    public static function instance($key)
    {        
        if(!isset(static::$instance[$key]) || !(static::$instance[$key] instanceof static))
        {
            static::$instance[$key] = new static($key);
        }
        return static::$instance[$key];
    }
}
