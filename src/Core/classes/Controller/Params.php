<?php
namespace Core\Controller;

//use Core\Controller;
use Core\Helper\Arr;
//use Core\Request;

trait Params //extends Controller\Controller
{

    protected function _call_param_key_method($request_params, $params_keys)
    {
        $params = array_reverse(Arr::intersect_keys($request_params, $params_keys));
        reset($params);       
        $method_name = (key($params)) ? key($params) : 'index';        
        $method_name = '_'.$method_name;
        
//        if(method_exists($this, $method_name))
//        {
          return call_user_func(array($this, $method_name), $params);
//        }
    }
}