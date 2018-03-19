<?php
namespace Core\Exception;

use Core\Helper\Arr;

class HTTP_Exception extends Exception
{
    private $messages = array(
        '404'   => 'Page not found',
        '500'   => 'internal server error',
    );
    
    public function __construct($code, $message=NULL)
    {
        
//        echo $code; exit;
//        
        if($message === NULL) $message = Arr::get($this->messages, $code, "");
//        
//        $message = 'qq';
       
    //        $message = parent::getMessage();
    //        return;
                // Set the message
//            $message = empty($variables) ? $message : strtr($message, $variables);

                // Pass the message and integer code to the parent

            parent::__construct($message, NULL, $code, NULL);
            
//        if(Request::$current->is_initial())
        {
            self::$view = 'errors/http';
            \Core\Response::status($code);
        }
//        else self::$view = 'errors/empty'; 
    }
    
}

