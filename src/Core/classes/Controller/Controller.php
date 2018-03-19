<?php
namespace Core\Controller;

/**
 * The main Controller class. Executing into the Request.
 * 
 * @author JackRabbit
 */

use Core\Core;
use Core\View;
use Core\Request;
use Core\Response;
use Core\Model\Model;

abstract Class Controller implements \Core\Controller\ContollerInterface
{
    /**
     * array of the "global" variables.
     * Variabls are available from all instances of the class "Controller"
     * Uses by set_global() method
     * 
     * @var array 
     */
    protected static $_global_data = array();
    
    /**
     * insstance of the Request class
     * 
     * @var Request
     */
    protected $request;
    
    /**
     * insstance of the Response class
     * 
     * @var Response
     */
    protected $response;
    
    protected $module;
    protected $modpath;
    
    protected $max_age = 60;
    
    /**
     * Set Request and Response variables
     * Set "global" variables
     * Find the current module and path to them
     * 
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        
        $this->response = new Response();
        
        foreach(self::$_global_data AS $key=>$value) $this->$key = $value;
        
        $m = array_search($this->request->params('namespace'), Core::paths());
        $m = substr($m, strlen(SRCPATH));
        $this->module = str_replace('\\', '/', $m);
        $this->modpath = '/'.$this->module.'/';
    }
    
    /**
     * Set the global data static array
     * 
     * @param \Core\Controller\Traversable $key
     * @param \Core\Controller\Traversable $value
     */
    public static function set_global($key, $value=NULL)
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

    /**
     * Execute the Controller
     * first - execute before() method
     * second - execute main action method of the child controller
     * third - execute after() method
     * 
     * @param string $action
     */
    public function execute($action='index')
    {  
        // a valid action name must not have a prefix "_"
        $action = ltrim($action, '_');
        
        $this->_before();

        call_user_func(array($this, $action));

        $this->_after();
    }
    
//    public function redirect($param)
//    {
//        header()
//    }
    
    /**
     * only for beauty
     * 
     * @param string $view_name
     * @param array $data
     * @return View object
     */
    protected function _view($view_name, array $data=NULL)
    {
        return View::factory($view_name, $data);
    }
    
    /**
     * only for beauty
     * 
     * @param string $model_name
     * @return Model object
     */
    protected function _model($model_name, $params = NULL)
    {
        $class = $this->request->params('namespace').'\Model\\'.ucfirst($model_name);       
        return new $class($params);
    }
    
    /**
     * Function for client side cache
     * Set response headers "cache-control" etc
     * 
     * @param numeric $lifetime
     * @param string $etag
     * @return boolean
     */   
    protected function _cache_control($lifetime, $etag = NULL)
    {
        if($this->request->params('query')) return FALSE;
        
        $this->response->headers('cache-control', 'max-age='.$lifetime);

        if($etag)
        {   
            if($this->response->headers('cache-control'))
                $this->response->headers('cache-control', $this->response->headers('cache-control').', must-revalidate');
            else
                $this->response->headers('cache-control', 'must-revalidate');

            $this->response->headers('etag', $etag);
            
            if ($this->request->headers('if-none-match') AND (string) $this->request->headers('if-none-match') === $etag)
            {
                // No need to send data again
                $this->response->status(304, 'Not modified'); //->headers('etag', $etag);
                return TRUE;
            }
            else return FALSE;
        }
    }
    
    /**
     * Method execute before action
     */
    protected function _before(){}
    
    /**
     * Method execute after action
     */
    protected function _after() {}
}