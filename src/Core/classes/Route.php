<?php
namespace Core;

use Core\Helper\Arr;
use Core\Exception;

Class Route
{
    
    // Matches a URI group and captures the contents
    const REGEX_GROUP   = '\(((?:(?>[^()]+)|(?R))*)\)';

    // Defines the pattern of a <segment>
    const REGEX_KEY     = '<([a-zA-Z0-9_]++)>';

    // What can be part of a <segment> value
    const REGEX_SEGMENT = '[^/.,;?\n]++';

    // What must be escaped in the route regex
    const REGEX_ESCAPE  = '[.\\+*?[^\\]${}=!|]';
    
    
    public static $subdomain = NULL;
    public static $current = NULL;
    
    public static $_routes = array();
    
    protected $_filters = array();
    protected $_defaults = array();
    protected $_regex = array();
    protected $_route_regex;
    protected $_closure = NULL;
    protected $_required_method = NULL;
    protected $_subdomain = NULL;
    protected $_namespace = 'App';
    protected $_directory = '\Controller\\';
    
    protected $_uri = '';


//    public $route_namespace = '\\App\\Controller\\';

//    public $dirr;

    /**
     * Creates a new route. Sets the URI and regular expressions for keys.
     * Routes should always be created with [Route::set] or they will not
     * be properly stored.
     *
     *     $route = new Route($uri, $regex);
     *
     * The $uri parameter should be a string for basic regex matching.
     *
     *
     * @param   string  $uri    route URI pattern
     * @param   array   $regex  key patterns
     * @return  void
     * @uses    Core\Route::compile
     */
    public function __construct($uri = NULL)
    {
            if ($uri === NULL)
            {
                    // Assume the route is from cache
                    return;
            }

            if ( ! empty($uri))
            {
                    $this->_uri = $uri;
            }
            
//            $this->dirr = __FILE__;

//            if ( ! empty($regex))
//            {
//                    $this->_regex = $regex;
//            }

            // Store the compiled regex locally
//            $this->_route_regex = self::compile($uri, $regex);
    }
    
    public static function set($name, $uri = NULL)
    {
        $route = new self($uri);
//        $route->dirr = $dirr;
        return self::$_routes[$name] = $route;
    }
    
    public static function all()
    {
        return self::$_routes;  
    }
    
    /**
     * Process a request to find a matching route
     *
     * @param   object  $request Request
     * @param   array   $routes  Route
     * @return  array
     */
    public static function get_params(Request $request, $routes = NULL)
    {
            
            // Load routes
            $routes = (empty($routes)) ? self::all() : $routes;
            $params = NULL;
            
//            var_dump($routes); exit;

            if(I18n::detect_lang_method() === I18N::URI)
            {
//                $store_uri = $request->uri;
                $lang = I18n::_detect_lang_from_uri($request);
//                var_dump($lang);
            }
            
            foreach ($routes as $name => $route)
            {
                
//                echo $name.'<br>';
                
                if ($params = $route->matches($request))
                {
                    Route::$current = $name;
//                    $params['lang'] = (isset($lang)) ? $lang : NULL;
                    if(isset($lang))
                    {
                        $params['lang'] = $lang;
//                        $request->uri = $store_uri;
                    }
                    else
                    {
                        $params['lang'] = FALSE;
                    }
                    
//                    var_dump($params);
                    
//                    $params['dirr'] = $route->dirr;
                    
                    return $params;
                }
            }
            
            if(!Core::$errors)
            {
                if($this->is_initial())
                    throw new Exception\HTTPException('Not Found', 404);
                else return; //throw new Exception\NULL_Exception(404);
            }
            else throw new Exception\Exception('The requested route not match');
//            return NULL;
    }
    
    public function compile()
    {
//        $this->_uri = str_replace(array('ru', 'en'), '', $this->_uri);

        // The URI should be considered literal except for keys and optional parts
        // Escape everything preg_quote would escape except for : ( ) < >
        $expression = preg_replace('#'.self::REGEX_ESCAPE.'#', '\\\\$0', $this->_uri);

        if (strpos($expression, '(') !== FALSE)
        {
                // Make optional parts of the URI non-capturing and optional
                $expression = str_replace(array('(', ')'), array('(?:', ')?'), $expression);
        }

        // Insert default regex for keys
        $expression = str_replace(array('<', '>'), array('(?P<', '>'.self::REGEX_SEGMENT.')'), $expression);
        
//        var_dump($this->_regex); exit;

        if (!empty($this->_regex))
        {
                $search = $replace = array();
                foreach ($this->_regex as $key => $value)
                {
                        $search[]  = "<$key>".self::REGEX_SEGMENT;
                        $replace[] = "<$key>$value";
                }

                // Replace the default regex with the user-specified regex
                $expression = str_replace($search, $replace, $expression);
        }

        return '#^'.$expression.'$#uD';
    }
    
//    public function regex(array $regex = NULL)
//    {
//        if($regex === NULL)
//            return $this->_regex;
//        
//        $this->_regex = $regex;
//        return $this;
//    }
    
    
    /**
     * Provides default values for keys when they are not present. The default
     * action will always be "index" unless it is overloaded here.
     *
     *     $route->defaults(array(
     *         'controller' => 'welcome',
     *         'action'     => 'index'
     *     ));
     *
     * If no parameter is passed, this method will act as a getter.
     *
     * @param   array   $defaults   key values
     * @return  $this or array
     */
    public function defaults(array $defaults = NULL)
    {
            if ($defaults === NULL)
            {
                    return $this->_defaults;
            }

            $this->_defaults = $defaults;

            return $this;
    }
    
    /**
     * Filters to be run before route parameters are returned:
     *
     *     $route->filter(
     *         function(Route $route, $params, Request $request)
     *         {
     *             if ($request->method() !== HTTP_Request::POST)
     *             {
     *                 return FALSE; // This route only matches POST requests
     *             }
     *             if ($params AND $params['controller'] === 'welcome')
     *             {
     *                 $params['controller'] = 'home';
     *             }
     *
     *             return $params;
     *         }
     *     );
     *
     * To prevent a route from matching, return `FALSE`. To replace the route
     * parameters, return an array.
     *
     * [!!] Default parameters are added before filters are called!
     *
     * @throws  Kohana_Exception
     * @param   array   $callback   callback string, array, or closure
     * @return  $this
     */
    public function filter($filter)
    {
        
        
        if ( is_callable($filter))
        {
            $this->_filters[] = $filter;
        }
        elseif(is_array($filter))
        {
            $this->_regex += $filter;
        }

        return $this;
    }
    
    public function callback($callback)
    {
        if ( ! is_callable($callback))
        {
                throw new Exception\Exception('Invalid Route::callback specified');
        }
        
        $this->_closure = $callback;
        return $this;
    }
    
    public function method($method)
    {
//        $methods = func_get_args();
        $this->_required_method = array_map('strtoupper', func_get_args());
//        $this->_required_method = strtoupper($method);
        return $this;
    }
    
//    public function _namespace($namespace)
//    {
//        $this->_namespace = $namespace;
//    }


    public function subdomain($subdomain)
    {
//        if(!is_array($subdomain)) $subdomain = [$subdomain];
        
        if(Arr::is_assoc($subdomain))
        {
//            $this->_subdomain = array_keys($subdomain);
//            $this->_namespace = array_values($subdomain);
            $this->_namespace = Arr::get($subdomain, self::$subdomain);
        }
        return $this;
    }

    public function matches(Request $request)
    {
        $_route_regex = $this->compile();
        
//        echo $_route_regex; exit;
        
        
            // Get the URI from the Request
            $uri = trim($request->uri(), '/');
            
            if(!empty($_route_regex))
            {
                if ( ! preg_match($_route_regex, $uri, $matches)) //echo 'huy';
                        return FALSE;

                $params = array();
                foreach ($matches as $key => $value)
                {
                        if (is_int($key))
                        {
                                // Skip all unnamed keys
                                continue;
                        }

                        // Set the value for all matched keys
                        $params[$key] = $value;
                }
            }
            
//            echo self::$current;
//            var_dump($params);
            
            
            $params['subdomain'] = self::$subdomain;
            if($this->_subdomain)
            {
                if(isset($this->_subdomain[self::$subdomain]))
                {
                    $this->_namespace = $this->_subdomain[self::$subdomain];
                }
                else return FALSE;
            }
            
            if($this->_required_method && is_array($this->_required_method))
            {
                if($key = array_search('AJAX', $this->_required_method))
                {
                    unset($this->_required_method[$key]);
                    if($request->is_ajax() === FALSE) return FALSE; 
                }
                if(!in_array($request->method(), $this->_required_method)) return FALSE;
            }
            
            if($this->_closure)
            {
                call_user_func($this->_closure, $request);
                exit;
            }

            if($this->_defaults)
            {
                foreach ($this->_defaults as $key => $value)
                {
                        if ( ! isset($params[$key]) OR $params[$key] === '')
                        {
                                // Set default values for any key that was not matched
                                $params[$key] = $value;
                        }
                }
            }
            
//            if(!empty($params['module']))
//            {
//                $this->_namespace = ucfirst($params['module']);
//            }
            
            if(!empty($params['namespace']))
            {
                $params['namespace'] = ucfirst($params['namespace']);
            }
            else $params['namespace'] = $this->_namespace;
            
            if(!empty($params['directory']))
            {
                $this->_directory .= ucfirst($params['directory']).'\\';
            }
            
            
            if ($this->_filters)
            {
                foreach ($this->_filters as $callback)
                {
                    // Execute the filter giving it the route, params, and request
                    $return = call_user_func($callback, $this, $params, $request);

                    if ($return === FALSE)
                    {
                            // Filter has aborted the match
                            return FALSE;
                    }
                    elseif (is_array($return))
                    {
                            // Filter has modified the parameters
                            $params = $return;
                    }
                }
            }
            
            
            if ( ! empty($params['controller']))
            {
                    // PSR-0: Replace underscores with spaces, run ucwords, then replace underscore
//                    $params['controller'] = str_replace(' ', '_', ucwords(str_replace('_', ' ', $params['controller'])));
//                if($this->_namespace === NULL) return FALSE;
                
//                if(strpos($params['controller'], '\\') === 0)
//                {
//                    
//                    $arr = explode('\\', $params['controller']);
//                    if(count($arr) === 2) $params['controller'] = trim($params['controller'], '\\');
//                    elseif(count($arr) > 2)
//                    {
//                        array_shift($arr);
//                        $this->_namespace = ltrim(array_shift($arr), '\\');
//                        $this->_directory = '\\'.array_shift($arr).'\\';
//                        $params['controller'] = implode('\\', $arr);
//                    }
//                }
                
                if(!empty($params['namespace']))
                {
                    $params['class_controller'] = $params['namespace'].$this->_directory.ucfirst($params['controller']);
                }
                else
//                if(!isset($params['namespace']) || $params['namespace'] === NULL)
                {
//                    die('lala');
                    $paths = array_reverse(Core::paths());
                    foreach($paths AS $dir=>$namespace)
                    {
                        $controller = $namespace.$this->_directory.ucfirst($params['controller']);
//                        echo $controller.'<br>';
                        if(class_exists($controller))
                        {
//                            die($controller);
                            $params['namespace'] = $namespace;
                            $params['class_controller'] = $controller;
                            break;
                        }
                    }
                    if(empty($params['namespace'])) $params['namespace'] = $this->_namespace;
                    if(empty($params['class_controller'])) $params['class_controller'] = $params['namespace'].$this->_directory.ucfirst($params['controller']);
                }
//                else
//                {
//
//
////                    $params['namespace'] = $this->_namespace;
//
//                    $params['class_controller'] = $params['namespace'].$this->_directory.ucfirst($params['controller']);
//                }
                
            }
            
            
            
//            var_dump($params); 
//            exit;
//            self::$current = array_search($uri, self::$_routes);
            return $params;
    }
    
    public static function get($name)
    {
        if ( ! isset(Route::$_routes[$name]))
        {
            throw new Exception('The requested route does not exist: :route',
                    array(':route' => $name));
        }

        return Route::$_routes[$name];
    }
    
    public static function current()
    {
        return static::get(static::$current);
    }
    
	/**
	 * Generates a URI for the current route based on the parameters given.
	 *
	 *     // Using the "default" route: "users/profile/10"
	 *     $route->uri(array(
	 *         'controller' => 'users',
	 *         'action'     => 'profile',
	 *         'id'         => '10'
	 *     ));
	 *
	 * @param   array   $params URI parameters
	 * @return  string
	 * @throws  Core\Exception\Exception
	 * @uses    Core\Route::REGEX_GROUP
	 * @uses    Core\Route::REGEX_KEY
	 */
	public function uri(array $params = NULL)
	{
		if ($params)
		{
			// @issue #4079 rawurlencode parameters
			$params = array_map('rawurlencode', $params);
			// decode slashes back, see Apache docs about AllowEncodedSlashes and AcceptPathInfo
			$params = str_replace(array('%2F', '%5C'), array('/', '\\'), $params);
		}

		$defaults = $this->_defaults;

		/**
		 * Recursively compiles a portion of a URI specification by replacing
		 * the specified parameters and any optional parameters that are needed.
		 *
		 * @param   string  $portion    Part of the URI specification
		 * @param   boolean $required   Whether or not parameters are required (initially)
		 * @return  array   Tuple of the compiled portion and whether or not it contained specified parameters
		 */
		$compile = function ($portion, $required) use (&$compile, $defaults, $params)
		{
			$missing = array();

			$pattern = '#(?:'.Route::REGEX_KEY.'|'.Route::REGEX_GROUP.')#';
			$result = preg_replace_callback($pattern, function ($matches) use (&$compile, $defaults, &$missing, $params, &$required)
			{
				if ($matches[0][0] === '<')
				{
					// Parameter, unwrapped
					$param = $matches[1];

					if (isset($params[$param]))
					{
						// This portion is required when a specified
						// parameter does not match the default
						$required = ($required OR ! isset($defaults[$param]) OR $params[$param] !== $defaults[$param]);

						// Add specified parameter to this result
						return $params[$param];
					}

					// Add default parameter to this result
					if (isset($defaults[$param]))
						return $defaults[$param];

					// This portion is missing a parameter
					$missing[] = $param;
				}
				else
				{
					// Group, unwrapped
					$result = $compile($matches[2], FALSE);

					if ($result[1])
					{
						// This portion is required when it contains a group
						// that is required
						$required = TRUE;

						// Add required groups to this result
						return $result[0];
					}

					// Do not add optional groups to this result
				}
			}, $portion);

			if ($required AND $missing)
			{
//                            echo static::$current.' lala'; exit;
                            throw new Exception\Exception(
                                    'Required route parameter not passed: :param',
                                    array(':param' => reset($missing))
                            );
			}

			return array($result, $required);
		};

		list($uri) = $compile($this->_uri, TRUE);

		// Trim all extra slashes from the URI
		$uri = preg_replace('#//+#', '/', rtrim($uri, '/'));

//		if ($this->is_external())
//		{
//			// Need to add the host to the URI
//			$host = $this->_defaults['host'];
//
//			if (strpos($host, '://') === FALSE)
//			{
//				// Use the default defined protocol
//				$host = Route::$default_protocol.$host;
//			}
//
//			// Clean up the host and prepend it to the URI
//			$uri = rtrim($host, '/').'/'.$uri;
//		}

		return $uri;
	}

    
}