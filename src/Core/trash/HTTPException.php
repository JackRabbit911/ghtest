<?php
namespace Core\Exception;

//use Core\Request;

class HTTPException extends Exception
{
    
    public function __construct($message = "", $code = 0, array $variables = NULL, Exception $previous = NULL)
    {
       
    //        $message = parent::getMessage();
    //        return;
                // Set the message
            $message = empty($variables) ? $message : strtr($message, $variables);

                // Pass the message and integer code to the parent

            parent::__construct($message, $variables, $code, $previous);
            
//        if(Request::$current->is_initial())
        {
            self::$view = 'errors/http';     
        }
//        else self::$view = 'errors/empty'; 
    }
    
}

