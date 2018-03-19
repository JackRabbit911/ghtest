<?php
namespace Core;


/**
 * :KLUDGE: Это пиздец. Самый главный класс
 * Всякое такое.
 * 
 * [!!] lalala
 * 
 * @author JackRabbit
 * @author Kolosoft
 */


//use Core\Route;
use Core\Helper\Arr;
//use Core\HTTP;
//use Core\Cache;
//use Core\I18n;

//use Core\Exception;

//use Core\Module\Session;

//use Core\Pattern\Singletone;

Class Core
{
    use Pattern\Singletone;
    
    /**
     * @var  boolean  show errors YES/NO
     */
    public static $errors = TRUE;
    
    /**
     * @var  boolean  output cache YES/NO
     */
    public static $cache = TRUE;
    
    
    
//    public static $base_url = '/';
    
    
    public static $index_file = FALSE;
    
    
    /**
     * @var  array  cache for array-type config variables
     */
    private static $config_cache = [];
    
    
//    public static $gzip = TRUE; //FALSE;
    
    /**
     * @var  numeric|boolean  output cache compress ratio or FALSE
     */
    public static $compress = 0;
    
    /**
     * @var  numeric  cache life time in seconnds
     */
    public static $life_time = 60;
    
    /**
     * @var  array  all settings variables
     */
    public static $init = array();
    
    
    
    
    public static $vars = array();
    
    /**
     * Searches for files in the file system Webnigger depends array $paths
     * the first - in App module? then in modules, in the end - in Core module.
     * if $name param contents full path, beginning with "/", Search is performed only on the specified path.
     * if $array param is TRUE, return array of the paths that coincide.
     * 
     * @param type string $name
     * @param type string $folder
     * @param type string $ext
     * @param type boolean $array
     * @return boolean|string|array
     * @uses Core\Core::paths
     */
    public static function find_file($name, $folder=NULL, $ext=NULL, $array=FALSE)
    {
        if(!$ext) $ext = '.php';
        else $ext = '.'.$ext;
        
        $name = str_replace('/', DIRECTORY_SEPARATOR, $name);
        $name = trim($name, DIRECTORY_SEPARATOR);
        $file = SRCPATH.$name.$ext;
        
        if(is_file($file)) return $file;
        
        if(!empty($folder))
        {
            $folder = str_replace('/', DIRECTORY_SEPARATOR, $folder).DIRECTORY_SEPARATOR;
            
            if(strpos($folder, DIRECTORY_SEPARATOR) === 0)
            {
                $name = ltrim($name, DIRECTORY_SEPARATOR);
                $file = SRCPATH.$folder.$name.$ext;
                if(is_file($file)) return $file;
            }
        }
        
        $paths = self::paths();
        
        $found = [];        
        foreach($paths AS $path=>$namespace)
        {
            $file = $path.DIRECTORY_SEPARATOR.$folder.$name.$ext;
            if(is_file($file))
            {
                if($array) $found[] = $file;
                else return $file;
            }
        }
        
        return ($array) ? $found : FALSE;
    }
    
    /**
     * read config files in folders "config" in all loaded modules
     * The result of the function is cached.
     * $path param - is path in multidimensional array in dot-notation
     * 
     * @param string $file
     * @param string $path
     * @return array
     * @uses Core\Core::find_file
     * @uses Core\Helper\Arr::path
     * @uses Core\Core::$config_cache
     */
    public static function config($file, $path=NULL, $merge=FALSE)
    {
        if(!$array = Arr::get(self::$config_cache, $file))
        {
            if($filepath = self::find_file($file, 'config', 'php', $merge))
            {
                if(is_array($filepath))
                {
                    $filepath = array_reverse($filepath);
//                    $arr0 = array_shift($filepath);
                    
                    $array = [];
                    
                    foreach($filepath AS $f)
                    {
                        $replace = include $f;
                        $array = array_replace_recursive($array, $replace);
                    }
//                    return $array;
                }
                else $array = include $filepath;
                
                
                self::$config_cache[$file] = $array;
            }
            else return NULL;
        }
        else $array = self::$config_cache[$file];
        return ($path) ? Arr::path($array, $path) : $array;
    }
    
    public static function message($key, array $values=NULL, $lang=NULL)
    {
        if(!$key) return FALSE;
        
        if(!$lang) $lang = I18n::$current_lang;
        
        $table = I18n::load($lang, 'messages');
        
        if(empty($table)) $table = I18n::load(I18n::$base_lang, 'messages');
        
        if(empty($table)) return $key;
        
        $string = Arr::path($table, $key, Arr::get($table, 'default', $key));
        
        if(is_array($string)) return $string;
        
        return (!$values) ? $string : strtr($string, $values);
    }
    
    
    /**
     * Set and Get public static variables
     * For example: Core::vars($key) - get, Core::vars($key, $value) - Set.
     * You can use dot notation: Core::vars('foo.bar') for multidimensional array;
     * 
     * @return array|NULL
     * @uses Core\Helper\Arr::path
     * @uses Core\Helper\Arr::set_path
     */
    public static function vars()
    {
        $count_args = count(func_get_args());
        
        if($count_args === 0) return self::$vars;
        elseif($count_args === 1)
        {
            $key = func_get_arg(0);
            
            if(is_array($key))
                self::$vars = Arr::merge(self::$vars, $key);
            else return Arr::path(self::$vars, $key);
        }
        else
        {
            $key = func_get_arg(0);
            $value = func_get_arg(1);
            if(($key === FALSE || $key === NULL) && is_array($value))
                self::$vars = Arr::merge(self::$vars, $value);
            else Arr::set_path(self::$vars, $key, $value);
        }
    }
    
    public static function is_var($key)
    {
        $hash = md5('Webnigger is the best framework!');
        if(Arr::path(self::$vars, $key, $hash) === $hash) return FALSE;
        else return TRUE;
    }

    /**
    * Only for beauty.
    * @return array;
    */
    public static function paths()
    {
        return Autoload::$_paths;
    }
    
    /**
     * Set and Get enviroment variable
     * @param integer $env
     * @return integer
     */
    public static function enviroment($env = NULL)
    {
        if($env === NULL)
        {
            return Autoload::$enviroment;
        }
        elseif(is_int($env))
        {
            Autoload::$enviroment = $env;
        }
    }

    public static function compress($str)
    {
        if(empty(self::$compress)) return $str;
        else return gzdeflate($str, self::$compress);
    }
    
    protected static function uncompress($str)
    {
        if(self::$compress === FALSE) return $str;
        else return gzinflate($str);
    }
    
    
    /**
     * 1. detect base url (if project located in subfolder), define BASEDIR constant
     * 2. set error and shutdown handlers
     * 3. detect subdomain from subdomain index file
     * 4. set $errors and $cache variables depend of enviroment variable.
     * 5. load php-libraries files from modules (if there are) "App" - the first.
     * 6. load ini files. "Core" - the first.
     * 
     * @global string $subdomain
     */
    protected function __construct()
    {
        $base_url = (string)substr(dirname(SRCPATH), strlen($_SERVER['DOCUMENT_ROOT']));
        if(!empty($base_url)) $base_url = '/'.ltrim($base_url, DIRECTORY_SEPARATOR);
        define('BASEDIR', $base_url);
        unset($base_url);
        
        set_error_handler(array('Core\Exception\Exception', 'error_handler'));
        register_shutdown_function(array('Core\Exception\Exception', 'fatal_handler'));
        
        global $subdomain;
        Route::$subdomain = $subdomain;
                
        if(self::enviroment() < TESTING)
        {
            self::$errors = FALSE;
            Cache::$output = TRUE;
//            self::$cache = TRUE;
//            Cache::server('enable', TRUE);
//            Cache::client('enable', FALSE);
        }
        else
        {
            self::$errors = TRUE;
            Cache::$output = FALSE;
//            self::$cache = FALSE;
//            Cache::server('enable', FALSE);
//            Cache::client('enable', FALSE);
        }
     
        foreach(self::find_file('lib', NULL, NULL, TRUE) AS $libfile)
        {
//            var_dump($libfile);
            include $libfile;
        }
        foreach(array_reverse(self::find_file('init', NULL, NULL, TRUE)) AS $inifile) include $inifile;
        
//        Cache::$output = TRUE;
//        self::$errors = FALSE;
    }
    
    /**
     * running of the application
     */
    public function execute()
    {
        ob_start();
        
        try
        {   
//            $this->cache_client();
            
//            $url = HTTP::detect_url();
            
//            if(self::$cache === TRUE)
            if(Cache::$output === TRUE)
            {
//                if(Cache::server('query') === TRUE) $uri = HTTP::detect_uri();
//                else 
                    $uri = HTTP::detect_url();
                
                $cache = Cache::factory();
                $cache_key = md5($uri);                
                $output = $cache->get($cache_key);
                
                
                
            }
            else $output = FALSE;
           
            if($output === FALSE)
            {
                echo Request::initial()->execute();
                $output = ob_get_clean();
                
//                echo self::$cache; exit;
                               
                if(Cache::$output === TRUE)
                {
                    if(!isset($cache)) $cache = Cache::factory();
//                    $cache->delete();
                     $cache->set($cache_key, $output);
//                     Cache::client('etag', $etag);
                }
                
//                var_dump(Cache::client('etag'));
//                exit;
//                
//                if(isset($etag))
//                {
//                    var_dump($etag); 
////                    exit;
////                    $output = $etag;
//                }
            }  
        }
//        catch(\ReflectionException $e)
//        {
//            echo Exception\Exception::response($e);
//        }       
//        catch(\PDOException $e)
//        {
//            echo Exception\Exception::response($e);
//        }
        catch (\Exception $e)
        {
            
            while (ob_get_level()) {
                ob_end_clean();
            }
            echo Exception\Exception::response($e);
        }
        catch (Exception\Exception $e)
        {
            while (ob_get_level()) {
                ob_end_clean();
            }
            echo $e::response($e);
        }    
        
        echo $output;       
    }
        
}