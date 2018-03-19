<?php
namespace Form\Controller;

use Core\Helper\URL;
use Core\Controller;
use Core\Validation;
/**
 * Description of Index
 *
 * @author JackRabbit
 */
class Form extends Controller\Post
{
    
    public function index()
    {
        $class = URL::url2ns($this->request->params('class'));
        $method = $this->request->params('method', 'index');
        
        $form = call_user_func([$class, $method]);
        
//        var_dump($form->_rules);
        
        if(!empty($this->post))
        {
            $form->validation($this->post);
            if($this->request->is_ajax())
            {
                echo $form->response();
            }
            else echo $form;
        }
        else 
            echo $form;
    }
    
    public function index1()
    {
        $class = URL::url2ns($this->request->params('class'));
        $method = $this->request->params('method', 'index');
        
        $form = call_user_func([$class, $method]);
        
        if(!empty($this->post))
        {
            $validation = new Validation();
            $validation->set_rules($form->rules);
            
            
//            var_dump($validation->_rules);
            
            if($validation->check($this->post))
            {
                if(!empty($form->func->success))
                    call_user_func_array($form->func->success, ['post'=>$this->post, 'form'=>&$form]);

                foreach($form->fields AS $name=>$field)
                {
                    $field->removeAttr('class', $field->attr['class_error']);
                    $field->addAttr('class', $field->attr['class_success']);
                    $field->addAttr('value', $this->post[$name]);
                }
            }
            else
            {
                foreach($form->fields AS $name=>$field)
                {
                    if(!empty($validation->response[$name]['msg']))
                    {
                        $field->removeAttr('class', $field->attr['class_success']);
                        $field->addAttr('class', $field->attr['class_error']);
                        $field->addAttr('value', NULL);
                        
                        if($form->show_msg === 'plh')
                        {
                            $field->addAttr('plh', $validation->response[$name]['msg']);
                            $field->addAttr('msg', NULL);
                        }
                        else
                        {
//                            $field->addAttr('msg', $validation->response[$name]['msg']);
//                            echo($validation->response[$name]['msg']);
                            $field->addAttr('msg', $validation->response[$name]['msg']);
//                            $field->addAttr('plh', $validation->response[$name]['msg']);
                            
//                            echo($form->fields['username']->getAttr('msg')); //exit;
                        }
                    }
                    else
                    {
                        $field->removeAttr('class', $field->attr['class_error']);
                        $field->addAttr('class', $field->attr['class_success']);
                        $field->addAttr('value', 'qqq');
                    }
                    
//                    $field->removeAttr('class');
                }
            }
        }
        
//        var_dump($form->fields['username']);
//        echo '<br>';
        
        
        
        echo $form->render();
    }
    
    public function auth()
    {
        $user = Auth::instance();
//        $model = new Model($user);
        
        $form_log_out = $this->_view('test/log_out', ['user'=>$user]);
        
        if($user->id) 
        {          
            echo $form_log_out;
            return;
        }
        
        $form = Form::set('log_in', 'test/log_in')
                ->attr('class', 'wn-form')
                ->input('name', ['type'=>'text', 'label'=>'Имя или email'], ['username', 'required'])
                ->input('pass', ['type'=>'text', 'label'=>'Пароль'], ['password'])
                ->input('remember', ['type'=>'checkbox', 'label'=>['Запомнить'], 'value'=>['on']]);
        
        if($form->check($this->post))
        {
            $name_or_email = $this->post['name'];
            $password = Arr::get($this->post, 'pass', '');
            
//            var_dump($this->post); exit;
            
            if($user_id = Model::log_in($name_or_email, $password))
            {   
                $user->regenerate($user_id);
                
                if(isset($this->post['remember'][0]) && $this->post['remember'][0] === 'on')
                {
                    Token::instance()->set($user_id);
//                    var_dump($t); exit;
                }
//                else
//                {
//                    print_r($this->post); exit;
//                }
                
                if(!$this->request->is_ajax())
                {   
                    HTTP::redirect(HTTP::referer());
//                    print_r($this->post); exit;
                }
                else
                {
                    echo $form_log_out;
                    return;
                }
            }
            else
            {
                $form->input('name', ['class'=>'has-error', 'value'=>'', 'error'=>'']);
                $form->input('pass', ['class'=>'has-error', 'value'=>'', 'error'=>Core::message('pair')]);
            }
        }
                        
        echo $form->render();
    }
    
    public function log_out()
    {
        User::instance()->log_out();
        
        if(!$this->request->is_ajax())
        {   
            HTTP::redirect(HTTP::referer());
        }
    }
    
}
