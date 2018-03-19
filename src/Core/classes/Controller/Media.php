<?php
namespace Core\Controller;

use Core\Controller\Controller;
//use Core\Core;
//use Core\View;



//use Core\Controller;
use Core\Core;
use Core\Cache;
use Core\Helper\File;
use Core\HTTP;

class Media extends Controller
{
    /**
     * @var numeric
     */
    public $max_age = 60;
    
    /**
     * @var string
     */
    public $etag = NULL;
    
    /**
     * @var string
     */
    public $dir = 'media';

    public function index()
    {   
        Cache::$output = FALSE;

        // Get the file path from the request
        $file = $this->request->params('file');
                               
         // Find the file extension
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        
         // Remove the extension from the filename
        $file = substr($file, 0, -(strlen($ext) + 1));
        
        // Remove query substr
        $ext = explode('?', $ext)[0];
        
        if ($file = Core::find_file($file, $this->dir, $ext))
        {
            //Custom cache policy
            $this->_cache_policy($file, $ext);
                        
            //Check cache and send headers (defined in Core\Controller\Controller)
            if(!$this->_cache_control($this->max_age, $this->etag))
            {
                // Send the file content as the response
                echo file_get_contents($file);
                $this->response->headers('content-type',  File::mime_by_ext($ext));
            }
        }
        else
        {
            $this->response->status(404);
        }       
    }
    
    /**
     * Custom client - cache policy
     * 
     * @param string $file
     * @param string $ext
     * @return \stdClass max-age and etag for send headers
     */
    private function _cache_policy($file, $ext)
    {
//        $obj = new \stdClass();
        
        if(stripos($file, 'vendor') !== FALSE)
        {
            $this->max_age = 30;
            $this->etag = NULL;
        }        
        elseif(in_array(strtolower($ext), ['png', 'jpg', 'jpeg', 'gif']))
        {
            $this->max_age = 30;
            $this->etag = NULL; // md5($this->request->url().filemtime($file));
        }
        else 
        {
            $this->max_age = 0;
            $this->etag = md5($this->request->url().filemtime($file));
        }
            
//        return $obj;
    }
}