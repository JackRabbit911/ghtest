<?php
namespace Core\Request;

use Core\Core;
use Core\Request;
use Core\Response;
use Core\Route;
use Core\Exception;
use Core\HTTP;
//use Core\Cache;
//use Core\I18n;
use Core\Helper\Arr;
use Core\Request\Accept;
//use Core\Request\Headers;

Class Internal extends Request
{
    public $accept;
//    public $headers;
    
    /**
     * Instance of the main Request
     * 
     * @var Request object
     */
    protected static $_initial;
    
    /**
     * Instance of the current Request
     * 
     * @var Request object
     */
    protected static $_current;
    
    
    protected static $_uri;
    protected static $_url;
    protected static $_query = array();
    protected $_post = array();   
    protected $_method = 'GET';
    protected $_params = array();   
    protected $_client_ip = NULL;    
    protected $_requested_with = NULL;
    
    
    
    /**********************************************/
    /**
     * @var  string  trusted proxy server IPs
     */
    protected static $trusted_proxies = array('127.0.0.1', 'localhost', 'localhost.localdomain');
    
    protected $_referrer = '';
    
    protected $_user_agent = '';
    
    protected static $_headers;


    public function __construct($uri = NULL)
    {
        if($uri === NULL) 
        {            
            static::$_uri = HTTP::detect_uri();
        }
        else static::$_uri = $uri;
        
        $this->accept = new Accept();
        
        self::$_headers = apache_request_headers();
        
//        $this->headers = new Headers();
    }
    
    public static function headers($key = NULL)
    {
        if($key === NULL) return self::$_headers;
        else
        {
            $headers = array_change_key_case(self::$_headers);
            return Arr::get($headers, strtolower($key));
        }
    }
 
    public static function uri()
    {
        if(!static::$_uri) static::$_uri = HTTP::detect_uri();
        return static::$_uri;
    }
    
    public static function url()
    {
        if(!static::$_url) static::$_uri = HTTP::detect_url();
        return static::$_url;
    }
    
    public function execute()
    {
        self::$_current = $this;
        
        if(empty($this->_params))
        {
            $this->_params = Route::get_params($this); //$this->process();            
        }
        
        if(file_exists(SRCPATH.'Core/hook.php')) include SRCPATH.'Core/hook.php';
        
        $filepath = array_search(Arr::get($this->_params, 'namespace'), Core::paths());
        $hook = $filepath.DIRECTORY_SEPARATOR.'hook.php';
        if(is_file($hook)) include $hook;
        
        if(!empty($this->_params['class_controller']))
        {  
            $controller = (class_exists($this->_params['class_controller'])) ? $this->_params['class_controller'] : FALSE;   
            $action =  Arr::get($this->_params, 'action', 'index');
            $action = (method_exists($controller, $action)) ? $action : FALSE;
            
            if(!$controller || !$action)
            {                                
                if(!Core::$errors)
                {                    
                    if($this->initial())
                    {
                        Response::status(404);
                        throw new Exception\HTTP_Exception(404, 'Not found');
                    }
                    else
                    {                        
                        Response::status(404);
                        return;
                    }
                }
                else
                {
                    if(!$controller)
                    {
                        throw new Exception\Exception('Controller ":controller" in route ":uri" not found',
                                array(':controller'=>$this->_params['class_controller'], ':uri'=>Route::$current), 404);
                    }
                    
                    if(!$action)
                    {
                        throw new Exception\Exception('Action ":action" in controller ":controller" not found, route ":route"',
                                array(':controller'=>$controller, ':action'=>$this->_params['action'], ':route'=>Route::$current));
                    }
                }
            }
 
            $controller = new $controller($this);
            
            try
            {
                ob_start();
                $controller->execute($action);
                return ob_get_clean();
            }
            catch(Exception\Exception $e)
            {
                echo $e::response($e);
                exit(1);
            }           
        }
    }

    public function params($params = NULL, $default = NULL)
    {
        if($params === NULL) return $this->_params;
        elseif(is_array($params))
        {
            $this->_params = array_merge($this->_params, $params);
            return $this;
        }       
        elseif(is_string ($params))
        {
//            if(isset($this->_params[$params]))
//            return $this->_params[$params];
//            else return NULL;
            return Arr::get($this->_params, $params, $default);
        }
        else throw new Exception('Invalid argument in function "params()"');
    }
    
    public function method($method = NULL)
    {
        if ($method === NULL)
        {
            if (isset($_SERVER['REQUEST_METHOD']))
            {
                // Use the server request method
                $this->_method = $_SERVER['REQUEST_METHOD'];
            }
                // Act as a getter
            return $this->_method;
        }

        // Act as a setter
        $this->_method = strtoupper($method);

        return $this;
    }
    
    public static function query($key=NULL)
    {
        if(empty(static::$_query)) parse_str(parse_url(static::uri(), PHP_URL_QUERY), static::$_query);
        if($key === NULL) return static::$_query;
        else return Arr::get(static::$_query, $key);
    }
    
    public function post($post = NULL)
    {
        if(empty($this->_post)) $this->_post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        
//        return Arr::get($this->_post, $post, 'huy');
        
        
        if($post === NULL) return $this->_post;
        elseif(is_array($post))
        {
            $this->method('post');
            $this->_post = $post;
            return $this;
        }
        else return Arr::get($this->_post, $post);
    }
    
    /**
     * Gets and sets the requested with property, which should
     * be relative to the x-requested-with pseudo header.
     *
     * @param   string    $requested_with Requested with value
     * @return  mixed
     */
    public function requested_with($requested_with = NULL)
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']))
        {
            // Typically used to denote AJAX requests
            $this->_requested_with = strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
        }

        if ($requested_with === NULL)
        {
                // Act as a getter
                return $this->_requested_with;
        }

        // Act as a setter
        $this->_requested_with = strtolower($requested_with);

        return $this;
    }

    /**
     * Returns whether this is an ajax request (as used by JS frameworks)
     *
     * @return  boolean
     */
    public function is_ajax()
    {
        return ($this->requested_with() === 'xmlhttprequest');
    }


    /**
     * Returns the first request encountered by this framework. This will should
     * only be set once during the first [Request::factory] invocation.
     *
     *     // Get the first request
     *     $request = Request::initial();
     *
     *     // Test whether the current request is the first request
     *     if (Request::initial() === Request::current())
     *          // Do something useful
     *
     * @return  Request
     * @since   3.1.0
     */
    public static function initial($uri = NULL)
    {
        if(!self::$_initial) self::$_initial = new self($uri);
        return self::$_initial;
    }

    /**
     * Returns whether this request is the initial request Kohana received.
     * Can be used to test for sub requests.
     *
     *     if ( ! $request->is_initial())
     *         // This is a sub request
     *
     * @return  boolean
     */
    public function is_initial()
    {
            return ($this === static::$_initial);
    }

    public static function current()
    {
        return self::$_current;
    }

    public function protocol()
    {
        return filter_input(INPUT_SERVER, 'REQUEST_SCHEME', FILTER_SANITIZE_URL);
    }

    public function domain()
    {
        return filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_URL);
    }

    public function route()
    {
        return Route::current();
    }

    public function client_ip()
    {
//        if(!$this->_client_ip)
//        {
//            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
//                AND isset($_SERVER['REMOTE_ADDR'])
//                AND in_array($_SERVER['REMOTE_ADDR'], static::$trusted_proxies))
//            {
//                // Use the forwarded IP address, typically set when the
//                // client is using a proxy server.
//                // Format: "X-Forwarded-For: client1, proxy1, proxy2"
//                $client_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
//
//                $this->_client_ip = array_shift($client_ips);
//
//                unset($client_ips);
//            }
//            elseif (isset($_SERVER['HTTP_CLIENT_IP'])
//                    AND isset($_SERVER['REMOTE_ADDR'])
//                    AND in_array($_SERVER['REMOTE_ADDR'], static::$trusted_proxies))
//            {
//                // Use the forwarded IP address, typically set when the
//                // client is using a proxy server.
//                $client_ips = explode(',', $_SERVER['HTTP_CLIENT_IP']);
//
//                $this->_client_ip = array_shift($client_ips);
//
//                unset($client_ips);
//            }
//            elseif (isset($_SERVER['REMOTE_ADDR']))
//            {
//                // The remote IP address
//                $this->_client_ip = $_SERVER['REMOTE_ADDR'];
//            }
//        }
//
//        return $this->_client_ip;
        
        return HTTP::client_ip();
        
    }
    
    public function user_agent()
    {
//        if(empty($this->_user_agent))
//            if (isset($_SERVER['HTTP_USER_AGENT']))
//                $this->_user_agent = $_SERVER['HTTP_USER_AGENT'];
//            
//            return $this->_user_agent;
        return HTTP::user_agent();
    }
    
    public function referrer()
    {
        if(empty($this->_referrer))
        {
            if(isset($_SERVER['HTTP_REFERER']))
                $this->_referrer = $_SERVER['HTTP_REFERER'];
            else $this->_referrer = BASEDIR;
        }
        
        return $this->_referrer;
    }
}