<?php
namespace Core;

/**
 * Autoload the modules.
 * 
 * @author Webnigger
 */
class Autoload
{        
    /**
     * array of paths to the loading modules
     * @var array
     */
    public static $_paths;
    
    /**
     * @var integer 
     */
    public static $enviroment = 10;
    
    /** 
     * Singletone Object of Core class 
     * @var  object
     */
    private static $instance;
    
    /**
     * Sinletone pattern create the object of this class
     * 
     * @return type Autoload
     */
    public static function instance()
    {        
        if(!(self::$instance instanceof self))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * $uses \Psr4AutoloaderClass()
     */
    public function __construct()
    {
        require_once SYSPATH.'vendor'.DIRECTORY_SEPARATOR.'Psr4Autoloader'.DIRECTORY_SEPARATOR.'Psr4AutoloaderClass.php';
        $this->psr4 = new \Psr4AutoloaderClass();
        
        self::$enviroment = filter_input(INPUT_SERVER, 'WN_ENV', FILTER_VALIDATE_INT);
    }
    
    /**
     * add module to spl_autoload_register
     * $folder - path to module from src (module_name)
     * if $namespace === NULL, $namespace will eq module name
     * 
     * @param string $folder
     * @param string $namespace
     * @return $this
     */
    public function addModule($folder, $namespace=FALSE)
    {
        if(empty($folder)) return;
        
        if(is_array($folder))
        {
            
            foreach($folder AS $key=>$value)
            {
                if(is_numeric($key))
                {
                    $folder = $namespace = $value;
                }
                else
                {
                    $folder = $key;
                    $namespace = $value;
                }
                $this->addModule($folder, $namespace);
            }
        }
        else
        {
        
            $folder = str_replace('/', DIRECTORY_SEPARATOR, $folder);
            $path = SRCPATH.$folder;

            if($namespace === FALSE)
            {
                $namespace = '';
                $arr = explode(DIRECTORY_SEPARATOR, $folder);
                foreach($arr AS $item) $namespace .= '\\'.ucfirst($item);

                $namespace = ltrim($namespace, '\\');
            }
            $this->psr4->addNamespace($namespace, $path.DIRECTORY_SEPARATOR.'classes');

            self::$_paths[$path] = $namespace;
        }
        
        return $this;
    }
    
    
    
    public function register()
    {
        $this->psr4->register();
    }
    
}

