<?php
namespace Core;

Class Request
{
    public static function internal($uri = NULL)
    {
        return new Request\Internal($uri);
    }
    
    public static function current()
    {
        return Request\Internal::current();
    }
    
    public static function initial($uri = NULL)
    {
        return Request\Internal::initial($uri);
    }
}