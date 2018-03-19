<?php
namespace User\Controller;

use Core\Controller;
use User\Auth;
use User\User;
use User\Token;
use Form\Form;
use Core\Helper\Arr;
use User\Model\User as Model;
use Core\Core;
use Core\HTTP;
/**
 * Description of Index
 *
 * @author JackRabbit
 */
class Index extends Controller\Post
{
    
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
