<?php
namespace Core\Exception;

use Core\Core;
use Core\Helper\Arr;

class Exception extends \Exception
{
//    public static $shutdown_errors = array(E_PARSE, E_ERROR, E_USER_ERROR);
    public static $php_errors = array(
		E_ERROR              => 'Fatal Error',
		E_USER_ERROR         => 'User Error',
		E_PARSE              => 'Parse Error',
		E_WARNING            => 'Warning',
		E_USER_WARNING       => 'User Warning',
		E_STRICT             => 'Strict',
		E_NOTICE             => 'Notice',
		E_RECOVERABLE_ERROR  => 'Recoverable Error',
		E_DEPRECATED         => 'Deprecated',
	);
    
    protected static $view = 'errors/error';
    
    public function __construct($message = "", array $variables = NULL, $code = 0, Exception $previous = NULL)
    {
            // Set the message
            $message = empty($variables) ? $message : strtr($message, $variables);

            // Pass the message and integer code to the parent
            parent::__construct($message, (int) $code, $previous);
//            parent::__construct();

            // Save the unmodified code
            // @link http://bugs.php.net/39615
//            $this->code = $code;
            
            while (ob_get_level()) {
                ob_end_clean();
            }
    }
    
    public function __toString()
    {
        return self::text($this);
    }
    
    public static function error_handler($errno, $errstr, $errfile, $errline)
    {
        if(headers_sent()) return;
        
//        ini_set('display_errors', 'Off');
//        error_reporting(0);
        
//        echo $errno; exit;

        if (error_reporting() && $errno && Core::$errors)
        {
            while (ob_get_level()) {
                ob_end_clean();
            }
            $e = new \ErrorException($errstr, $errno, 0, $errfile, $errline);
            
//            var_dump($e->code); exit;

            static::response($e);
            exit(1);
        }
//        else echo 'ERROR';
        
//        echo 'ERROR';
        
    }
    
    public static function fatal_handler()
    {
//        return;
        
        if($error = error_get_last() AND $error['type'] & ( E_ERROR | E_PARSE | E_USER_ERROR | E_COMPILE_ERROR | E_CORE_ERROR))
        {
            
//            echo 'ERROR';
            
            while (ob_get_level())
            {
                ob_end_clean();
            }
            
            $e = new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']);
            
            if(Core::$errors)
                $e = new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']);
            else
                $e = new HTTP_Exception('Server Error', 500);
            
            
            static::response($e);
            exit(1);
        }
    }
    
    public static function text($e)
    {
        $file = substr($e->file, strlen(DOCROOT));
        $response = sprintf('%s [ %s ]: %s ~ %s [ %d ]',
                    get_class($e), 
                Arr::get(self::$php_errors, $e->code, $e->code), 
                strip_tags($e->message), 
                $file, 
                $e->line);
        
//        $trace = $e->getTraceAs
        
        return (string) $response.'<br>';
    }
    
    public static function response($e)
    {
        if(Core::$errors === FALSE) $e = new HTTP_Exception($e->code);
//        return $ee->response($ee);
        
        
//        echo $e->getCode();
//        return;  $e->getCode();
        
        
//        $er = new HTTPException('Not Found', 404);
//        return $er->response($er);
        
        try
        {
            $class   = get_class($e);
            $code    = Arr::get(self::$php_errors, $e->code, $e->code); // $e->getCode();
            $message = $e->getMessage();
            $file    = $e->getFile();
            $line    = $e->getLine();
            $trace   = $e->getTrace();
//            var_dump(get_defined_vars());
//            return '';
            
            $status = ($e instanceof HTTP_Exception) ? $e->code : 500;
            header('Content-Type: text/html; charset=UTF-8', TRUE, $status);
            echo \Core\View::factory(self::$view, get_defined_vars())->render();
            exit(1);
        } 
        catch (Exception $e)
        {
            
                echo $e->text($e);
          
        }
        
    }
}

