<?php
namespace Core\Controller;

use Core\Controller\Controller;

/**
 * Description of Index
 *
 * @author JackRabbit
 */
class Post extends Controller
{
    protected $post;
    
    protected function _before()
    {
        parent::_before();
        
//        var_dump($this->request->post()); exit;
        
        if($this->request->is_ajax() && !empty($this->request->post('form_data')))
        {
            $this->post = $this->_unserialize($this->request->post('form_data'));
        }
        else $this->post = $this->request->post();
    }
    
    protected function _unserialize($array)
    {
        $result = [];
        foreach($array AS $item)
        {
            $arr = preg_split('/\[(\d+)\]$/', $item['name'], 2, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            
            $name = $arr[0];
            
            if(isset($arr[1]))
            {
                $key = $arr[1];
                $result[$name][$key] = $item['value'];
            }
            else
            {
                $result[$item['name']] = $item['value'];
            }
            
        }
        return $result;
    }
    
}
