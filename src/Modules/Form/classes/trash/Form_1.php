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
use Core\Core;

class Form
{
    protected static $_forms = array();
    
    protected $form_name;
    protected $view = 'default';
    public $input = array();
    protected $attr = array();
    protected $rules = array();
//    public $name;
    
    public static function set($form_name, $view_name=NULL)
    {
//        return new static($name);
        
        if(empty(static::$_forms[$form_name]))
            static::$_forms[$form_name] = new static($form_name, $view_name);
        
        return static::$_forms[$form_name];
    }
    
    public function __construct($form_name, $view_name=NULL)
    {
        if($view_name === NULL) $this->view = $form_name;
        else $this->view = $view_name;
        
        $this->form_name = $form_name;
        
        $this->validation = new Validation();
    }


    public static function get($name)
    {
        if(isset(static::$_forms[$name])) return static::$_forms[$name];
        else return FALSE;
    }
    
    public function attr($key, $value)
    {
        $this->attr[$key] = $value;
        return $this;
    }
    
    public function input($name, array $attr = [], array $rule = [])
    {
        
        if(strpos($name, '[') !== FALSE)
        {
            $name = substr_replace($name, '', strpos($name, '['));
        }
                
        
        
        if(!isset($this->input[$name]['name'])) $this->input[$name]['name'] = $name;
        
        if(!isset($this->input[$name]['type']))
        {
            if(isset($attr['type'])) $this->input[$name]['type'] = $attr['type'];
            else $this->input[$name]['type'] = 'text';
        }
        elseif(isset($attr['type'])) $this->input[$name]['type'] = $attr['type'];
        
        if(!isset($this->input[$name]['label']))
        {
            if(isset($attr['label'])) $this->input[$name]['label'] = $attr['label'];
            else $this->input[$name]['label'] = $name;
        }
        elseif(isset($attr['label'])) $this->input[$name]['label'] = $attr['label'];
        
        if(!isset($this->input[$name]['plh']))
        {
            if(isset($attr['plh'])) $this->input[$name]['plh'] = $attr['plh'];
            elseif(is_string($this->input[$name]['label'])) $this->input[$name]['plh'] = 'Введите '.mb_strtolower($this->input[$name]['label']);
            else $this->input[$name]['plh'] = NULL;
        }
        elseif(isset($attr['plh'])) $this->input[$name]['plh'] = $attr['plh'];
        
        if(!isset($this->input[$name]['value']))
        {
            if(isset($attr['value'])) $this->input[$name]['value'] = $attr['value'];
            else $this->input[$name]['value'] = NULL;
        }
        elseif(isset($attr['value'])) $this->input[$name]['value'] = $attr['value'];
        
        if(!isset($this->input[$name]['class']))
        {
            if(isset($attr['class'])) $this->input[$name]['class'] = $attr['class'];
            else $this->input[$name]['class'] = 'default';
        }
        elseif(isset($attr['class'])) $this->input[$name]['class'] = $attr['class'];
        
        if(!isset($this->input[$name]['error']))
        {
            if(isset($attr['error'])) $this->input[$name]['error'] = $attr['error'];
            else $this->input[$name]['error'] = NULL;
        }
        elseif(isset($attr['error'])) $this->input[$name]['error'] = $attr['error'];
        
        if(isset($attr['vars'])) $this->input[$name]['vars'] = $attr['vars'];
        
        $this->rules[$name] = $rule;
        
        return $this;
    }
    
    public function rule()
    { 
        $args = func_get_args();

        $name = array_shift($args);
        $func = array_shift($args);
        
        $this->validation->_rule($name, $func, $args);
        
        return $this;
    }
    
    public function rules()
    {
        $args = func_get_args();
        $name = array_shift($args);
        
        foreach($args AS $func)
        {
            $this->validation->_rule($name, $func);
        }
    }
    
    public function check($post)
    {        
        if(empty($post)) return FALSE;
        
        $this->validation->set_rules($this->rules);       
        $ok = $this->validation->check($post);
        
        foreach($this->input AS $name=>$input)
        {            
            $default = ['value'=>NULL, 'error'=>NULL];           
            $validate[$name] = Arr::get($this->validation->response, $name, $default);
            if($input['type'] === 'checkbox')
            {
                $validate[$name]['value'] = $this->chb($post, $name);
            }           
        }
        
//        var_dump($validate); exit;
        
        foreach($validate AS $field=>$response)
        {
            if($response['error'])
            {
                $msg = Core::message($response['error'], [':field'=>$this->input[$field]['label']]);
                $this->input($field, ['class'=>'has-error', 'error'=>$msg]);
            }
            else
            {
                $this->input($field, ['class'=>'has-success', 'value'=>$response['value']]);
            }
            
        }
        
        return $ok;
    }
    
    protected function chb($post, $name)
    {
        $r = [];
        
        
        foreach($post AS $key=>$value)
        {
            if(strpos($key, $name) !== FALSE)
            {
                preg_match('/\[(\d+)\]$/', $key, $m);
                $num = Arr::get($m, 1);

                $num = (integer) $num;
                $r[$num] = $value;
            }
        }

        foreach($this->input[$name]['label'] AS $k=>$label)
        {
            if(!isset($r[$k])) $r[$k] = 'off';
        }
        ksort($r);
        return $r;
    }

    public function render()
    {
        $form = View::factory($this->view);

        $form->form_name = $this->form_name;
        
        if(!isset($this->attr['class'])) $this->attr['class'] = 'default';
        
        foreach($this->attr AS $attr=>$value)
        {
            $form->$attr = $value;
        }
        
//        $form->inputs = $this->input;
        
        $form->input = '';
        
        foreach($this->input AS $name=>$field)
        {
//            $form->$name = (object) $field;
            if($field['type'] === 'textarea')
            {
            
            }
            elseif($field['type'] === 'checkbox')
            {
                
                $view = View::factory('checkbox', $field);
            }
            elseif($field['type'] === 'radio')
            {
                $view = View::factory('radio', $field);
            }
            elseif($field['type'] === 'select')
            {
                
            }
            else
            {
                $view = View::factory('input', $field);
            }
            
            $form->input .= $view->render();
            
        }
        
//        exit;
        
        return $form->render();
        
    }
    
    protected function checkbox($field, $name, $value)
    {
        $arr_val[$field][$name] = $value;
                                       
        $a = []; $max = 0; $r = [];

        if(!empty($arr_val))
        {
            foreach($arr_val AS $f=>$n)
            {
                foreach($n AS $k=>$v)
                {
                    preg_match('/\[(\d+)\]$/', $k, $m);
                    $num = Arr::get($m, 1);

                    $num = (integer) $num;

                    if($max < $num) $max = $num;

                    $a[$num] = $v;
                }

            }
        }

        for($i = 0; $i < $max+1; $i++)
        {
            if(isset($a[$i])) $r[] = $a[$i];
            else $r[] = 'off';
        }
        
        return $r;
        
    }
    
}
