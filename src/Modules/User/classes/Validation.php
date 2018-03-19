<?php
namespace User;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Form
 *
 * @author JackRabbit
 */
//use Core\Pattern\DataIterator;
//use Core\Session;
//use User\Model\Auth as ModelAuth;
use Core\Core;
use Core\Helper\HTML;
use Core\Session;
//use Form;

class Validation
{
//    protected $data = [];
    public static function auth($post, &$form)
    {  
//        echo json_encode(['error'=>'ERROR']);
        
        $user_id = Model\User::log_in($post['username'], $post['password']);
        
        if($user_id === FALSE)
        {
            $session = Session::instance();
            $session->start();
            $count = $session->get('try', 0);
            $session->set('try', ++$count);
            
            if($count < 3)
            {
                $msg = Core::message(__FUNCTION__);
//                $msg .= '<br>'.HTML::anchor('/', 'Забыли пароль?');
                $ostatok = 4 - $count;
                $msg .= ' Осталось попыток: '.$ostatok;
                $msg .= '<br>'.time() - $session->last_activity();
//                $form->username->status('error');
//                $form->password->status('error', $msg);
                
            }
            else
            {
                $msg = 'To many tryes';
                $form->password->attr('disabled', 'disabled');
//                $msg .= '<br>'.HTML::anchor('/', 'Забыли пароль?');
                $session->delete('try');
            }
            
//            $msg .= '<br>'.HTML::anchor('/', 'Забыли пароль?');
            
            $msg .= '<br>'.$count;

            $form->username->status('error');
            $form->password->status('error', $msg);
            
//            $form->password->attr('plh', 'qqq');
        }
        else
        {
//            $form->username->attr('value', $post['username']);
//            $form->username->attr('class', $form::$class['success']);
//            $form->password->attr('class', $form::$class['warning']);
//            $form->password->attr('msg', 'To easy, nah');
            
            $form->username->status('success');
            $form->password->status('warning', 'To easy, nah');
//            $form->password->attr('msg', 'To easy, nah');
            
//            $response = [
//                'username'  => $form->username->render(),
//                'password'  => $form->password->render(),
//            ];
//            
//            echo json_encode($response);
            
//            return TRUE;
            
            
            
//            $key = $form->attr('id');
            
//            $key = 'password';
            
            $form->response('GOOD FOR YOU!');
            
//            return $data;
//            exit;
            
        }
        
//        $form->response('username');
//        $form->response('password');
        
//        return $form;
        
//        $response = [
//                'username'  => $form->username->render(),
//                'password'  => $form->password->render(),
//            ];
//        
//        echo json_encode($response);
    }
    
    public static function auth1($value, $login_field = 'username', $name = ':name', $validation = ':valid')
    {
        $login = $validation->response[$login_field ]['value'];
        
        $user_id = Model\User::log_in($login, $value);
        
        if($user_id === FALSE)
        {
            $validation->response[$login_field]['value'] = 'error';
            $validation->response[$login_field]['status'] = 'error';
            $validation->response[$login_field]['code'] = __FUNCTION__;
            return FALSE;
        }
        else
        {
            $validation->response[$name]['status'] = 'warning';
            $validation->response[$name]['msg'] = 'To easy';
            return TRUE;
        }
    }

    public static function is_unique($value, $field, $validation = ':valid', $name = ':name')
    {
        $result = Model\User::is_unique($field, $value);
        
        if($result === TRUE) return TRUE;
        else
        {
//            if(!$name) $name = $field;
            $validation->response[$name]['code'] = $field.'-unique';
            return FALSE;
        }
    }
   
}
