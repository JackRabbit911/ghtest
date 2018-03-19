<?php
namespace Form;
/**
 * Description of Form
 *
 * @author JackRabbit
 */

use Core\Helper\Arr;
use Core\View;
use Core\Validation;
use Core\Pattern\DataIterator;

class Form extends DataIterator
{
    use FormField;
    
    protected $response = [];    
    protected $check = TRUE;    
    protected $view_name = 'form';
    protected $_rules = array();   
    protected $fields_css = array();
    protected $fields_js = array();
    protected $func;
    protected $attr = array(
        'name'  => NULL,
        'action'  => NULL,
        'method' => 'post',
        'value' => NULL,
        'plh'   => NULL,
        'class' => NULL,
        'id'    => NULL,
    );
    
    public function __construct($name, $view_name=NULL)
    {
        if($view_name === NULL) 
            $this->view_name = $this->view_path.$this->view_name;
        else $this->view_name = $view_name;
        
        $this->attr['name'] = $name;
        $this->attr['id'] = 'form'.uniqid().$name;
        
        $this->func = new \stdClass();        
        $this->func->error = [$this, 'funcErrorDefault'];
        $this->func->success = [$this, 'funcSuccessDefault'];
    }
    
    public function js($path)
    {
        $this->js[] = $path;
        return $this;
    }
    
    public function css($path)
    {
        $this->css[] = $path;
        return $this;
    }
    
    public function add($input)
    {
        $this->fields_css = Arr::merge($this->fields_css, $input->css);
        $this->fields_js = Arr::merge($this->fields_js, $input->js);
        $this->_rules[$input->attr['name']] = $input->_rules;
        $this->data[$input->attr['name']] = $input;
        return $this;
    }
    
    public function funcError($func)
    {
        $this->func->error = $func;
        return $this;
    }
    
    public function funcSuccess(callable $func)
    {
        $this->func->success = $func;
        return $this;
    }
    
    public function funcErrorDefault(&$form)
    {
//        echo json_encode($form->response);
//        return $form;
    }
    
    public function funcSuccessDefault($post, &$form)
    {
//        echo json_encode($form->response);
//        return $form;
    }
    
    public function validation($post)
    {
        $ok = TRUE;
        foreach($post AS $name => $value)
        {   
            $ok = $this->checkField($name, $value);            
//            $this->response[$name] = $this->$name->render();
        }
        
//        return $this->response;
        
        if($this->check)
        {
            return call_user_func_array($this->func->success, [$post, &$this]);
        }
        else
        {
            $_form = &$this;
            return call_user_func($this->func->error, $_form);
        }
//        
//        return $this;
    }
    
    public function check($post)
    {
        foreach($post AS $name => $value)
        {
            $this->checkField($name, $value);
        }
        return $this->check;
    }
    
    public function checkField($name, $value)
    {
        if(!$this->validation)
        {
            $this->validation = new Validation($this->_rules);           
        }
        
        $this->$name->validation = &$this->validation;
        
        $ok = $this->validation->checkField($name, $value);
        
        $status = $this->validation->response[$name]['status'];
        
        $this->$name->status($status);
        
//        $this->response[$name] = $this->$name->render();
        
        if($ok === FALSE) $this->check = $ok;
        
        return $ok;
    }
    
    public function response($data = NULL, $key = NULL)
    {
        if($data !== NULL && $key === NULL) $this->response = $data;
        elseif($data !== NULL && $key !== NULL) $this->response[$key] = $data;
        else
        {
            if(!empty($this->response) && !is_array($this->response))
                return $this->response;
            
            
//            $response = [];
            foreach($this->data AS $field)
            {
                if(empty($this->response[$field->attr['name']]))
                    $this->response[$field->attr['name']] = $field->render();
            }
            return json_encode($this->response);
        }
    }
    
    public function render()
    {
        if(!empty($this->response) && !is_array($this->response))
            return $this->response;
        
        $form = View::factory($this->view_name, $this->attr)
                ->set('fields', $this->data)
                ->css($this->css)
                ->css($this->fields_css)
                ->js($this->js)
                ->js($this->fields_js);
        
        return $form->render();
    }
}
