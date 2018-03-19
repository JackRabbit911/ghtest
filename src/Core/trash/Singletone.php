<?php
namespace Core;

trait Singletone
{
    /** 
     * Singletone Object of real class 
     * @var  object
     */
    public static $instance;
    
    /**
     * Singletone pattern
     * @return object this class
     */
    public static function instance()
    {        
        if(!(static::$instance instanceof static))
        {
            static::$instance = new static();
        }
        return static::$instance;
    }
}
