<?php
namespace Core;

/**
 * Description of Response
 *
 * @author JackRabbit
 */
use Core\Cache;
use Core\Helper\Arr;
use Core\HTTP;
use Core\Request;
use Core\Helper\File;

class Response
{
    const SERVER = FALSE;
    
//    private $request;


    public function __construct()
    {
//        $this->request = $request;
    }
    
    public function file($file, $dir)
    {
//        var_dump($this->request->query()); exit;
        
        // Find the file extension
        $ext = pathinfo($file, PATHINFO_EXTENSION);
         // Remove the extension from the filename
        $file = substr($file, 0, -(strlen($ext) + 1));
        // Remove query substr
        $ext = explode('?', $ext)[0];
        
        if ($file = Core::find_file($file, $dir, $ext))
        {
                // Check if the browser sent an "if-none-match: <etag>" header, and tell if the file hasn't changed
            $etag = md5(HTTP::detect_url().filemtime($file));
            
            
            
            if(!$this->client_cache($etag))
            {
                // Send the file content as the response
                echo file_get_contents($file);
                $this->headers('content-type',  File::mime_by_ext($ext));
            }
        }
        else
        {
            $this->status(404);
        }
    }
    
    public function client_cache($etag = NULL, $lifetime = NULL)
    {
//        $request = Request::initial();
//        
//        echo $request->method(); exit;
        
        
        $return = FALSE;
        
//        if(Request::initial()->method() !== 'GET') return $return;
        if(!empty(Request\Internal::query())) return $return;
        
        
        
        
        if($lifetime === NULL) $lifetime = 60;    
        if($etag === NULL) $etag = md5(HTTP::detect_url());
        
        if($this->headers('cache-control'))
            $this->headers('cache-control', $this->headers('cache-control').', must-revalidate');
        else
            $this->headers('cache-control', 'max-age='.$lifetime.', must-revalidate');
        
        $this->headers('etag', $etag);
        
        if (Request\Internal::headers('if-none-match') AND (string) Request\Internal::headers('if-none-match') === $etag)
        {
            // No need to send data again
            $this->status(304, 'Not modified'); //->headers('etag', $etag);
            $return = TRUE;
        }
//        else echo 'huy';
        
        return $return;
    }


    public function status($code, $message = NULL)
    {
//        return http_response_code($code);
//        if($message !== NULL) $message = ' '.$message;
//        header(self::protocol().' '.$status.$message);
        HTTP::status($code, $message);
        return $this;
    }
    
    public function headers($headers = NULL)
    {
        if(empty($headers)) return apache_response_headers();
        elseif(is_array($headers) && Arr::is_assoc($headers))
        {
            foreach($headers AS $key=>$value)
            {
                header((ucwords($key, '-').': '.$value));
            }
        }
        elseif(func_num_args() === 2 && is_string($headers))
        {
            header(ucwords($headers, '-').': '.func_get_arg(1));
            
        }
        elseif(func_num_args() === 1)
        {
            return Arr::get(apache_response_headers(), $headers);
        }
        
        return $this;
        
//        $args = func_get_args();
//        $count = func_num_args();
//        if($count == 0) $headers = NULL;
//        elseif($count == 1)
//        {
//            $headers = $args[0];
//            HTTP::header($headers);
//        }
//        else
//        {           
//            HTTP::header($headers, $args[1]);
//        }
//        return $this;
    }

    public function cache_control($key, $value)
    {
        if($key === self::SERVER)
        {
            Cache::lifetime($value);
        }
    }
}
