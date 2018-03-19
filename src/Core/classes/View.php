<?php
namespace Core;

//use Core\Helper\Arr;

use Core\Exception\Exception;
use Core\Core;

class View {
    
    public static $_global_data = array();
    public static $css = [];
    public static $js = [];
    public $_file;
    public $_data = array();
    
    
    
    public static function factory($file, $data = NULL)
    {  
        return new static($file, $data);
    }
    
    public static function set_global($key, $value = NULL)
    {
        if (is_array($key) OR $key instanceof Traversable)
        {
                foreach ($key as $name => $value)
                {
                        self::$_global_data[$name] = $value;
                }
        }
        else
        {
                self::$_global_data[$key] = $value;
        }
    }
    
    public function __construct($file, array $data = NULL)
    {
        $this->_file = $file;
        
        if ($data !== NULL)
        {
            // Add the values to the current data
            $this->_data = $data + $this->_data;           
        }
        
        
    }
    
       /**
     * Magic method, searches for the given variable and returns its value.
     * Local variables will be returned before global variables.
     *
     *     $value = $view->foo;
     *
     * [!!] If the variable has not yet been set, an exception will be thrown.
     *
     * @param   string  $key    variable name
     * @return  mixed
     * @throws  Core\Exception\Exception
     */
    public function & __get($key)
    {
            if (array_key_exists($key, $this->_data))
            {
                    return $this->_data[$key];
            }
            elseif (array_key_exists($key, View::$_global_data))
            {
                    return View::$_global_data[$key];
            }
            else
            {
                    throw new Exception('View variable is not set: :var',
                            array(':var' => $key));
            }
    }
    
    /**
     * Magic method, calls [View::set] with the same parameters.
     *
     *     $view->foo = 'something';
     *
     * @param   string  $key    variable name
     * @param   mixed   $value  value
     * @return  void
     */
    public function __set($key, $value)
    {
            $this->set($key, $value);
    }
    
     /**
     * Magic method, determines if a variable is set.
     *
     *     isset($view->foo);
     *
     * [!!] `NULL` variables are not considered to be set by [isset](http://php.net/isset).
     *
     * @param   string  $key    variable name
     * @return  boolean
     */
    public function __isset($key)
    {
        return (isset($this->_data[$key]) OR isset(self::$_global_data[$key]));
    }
    
    
    /**
     * Magic method, unsets a given variable.
     *
     *     unset($view->foo);
     *
     * @param   string  $key    variable name
     * @return  void
     */
    public function __unset($key)
    {
            unset($this->_data[$key], View::$_global_data[$key]);
    }
 
    public function __toString()
    {
        
//        return $this->render();
        
        try
        {
            return $this->render();
        }
        catch (Exception\Exception $e)
        {
            if(Core::$errors)
            {
                return $e->response($e);
//                exit(1);
            }
            else return '';
        }
        
    }
    
    /**
     * Assigns a variable by name. Assigned values will be available as a
     * variable within the view file:
     *
     *     // This value can be accessed as $foo within the view
     *     $view->set('foo', 'my value');
     *
     * You can also use an array or Traversable object to set several values at once:
     *
     *     // Create the values $food and $beverage in the view
     *     $view->set(array('food' => 'bread', 'beverage' => 'water'));
     *
     * [!!] Note: When setting with using Traversable object we're not attaching the whole object to the view,
     * i.e. the object's standard properties will not be available in the view context.
     *
     * @param   string|array|Traversable  $key    variable name or an array of variables
     * @param   mixed                     $value  value
     * @return  $this
     */
    public function set($key, $value = NULL)
    {
        if (is_array($key) OR $key instanceof Traversable)
        {
            foreach ($key as $name => $value)
            {
                    $this->_data[$name] = $value;
            }
        }
        else
        {
            $this->_data[$key] = $value;
        }

        return $this;
    }
    
    /**
     * Assigns a value by reference. The benefit of binding is that values can
     * be altered without re-setting them. It is also possible to bind variables
     * before they have values. Assigned values will be available as a
     * variable within the view file:
     *
     *     // This reference can be accessed as $ref within the view
     *     $view->bind('ref', $bar);
     *
     * @param   string  $key    variable name
     * @param   mixed   $value  referenced variable
     * @return  $this
     */
    public function bind($key, & $value)
    {
            $this->_data[$key] =& $value;

            return $this;
    }

    public function render()
    {       
        $filename = Core::find_file($this->_file, 'views');
        
        // Import the view variables to local namespace
        extract($this->_data, EXTR_SKIP);

        if (self::$_global_data)
        {
            // Import the global view variables to local namespace
            extract(self::$_global_data, EXTR_SKIP | EXTR_REFS);
        }
        
        ob_start();
        if(!empty($filename) && is_file($filename))
        {
            
            include $filename;
            return ob_get_clean();
        }
        else
        {
//            ob_end_clean();
            
            if(Core::$errors)
            {
//                return 'error';
                $e = new Exception('view ":file" not found', array(':file'=>$this->_file));
                return (string) $e->response($e);
            }
            else return '';
            
//            return $e;
        }
    }
    
    public function css()
    {
        $args = func_get_arg(0);
        if(!is_array($args)) $args = func_get_args();
        
        foreach($args AS $str)
        {
            if(!in_array($str, View::$css))
            {
                    View::$css[] = $str;
            }
        }
        return $this;
    }
    
    public function js()
    {
        $args = func_get_arg(0);
        if(!is_array($args)) $args = func_get_args();
        
        foreach($args AS $str)
        {
//            echo $str.'<br>';
            
//            $str = '/'.ltrim($str, '/');
            
            if(!in_array($str, View::$js))
            {
                    View::$js[] = $str;
            }
        }
        return $this;
    }
}
