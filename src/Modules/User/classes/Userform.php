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


use Form;

class Userform
{
//    protected $data = [];
    
    public static function auth()
    {
        $username = Form\Input::factory('username')
                ->attr('type', 'text')
                ->attr('label', 'Имя или email!')
                ->attr('plh', 'Введите имя или email, бля')
                ->rule('required', ':username')
                ->rule('min_lenth', 5);
        
        $password = Form\Input::factory('password')
                ->attr('type', 'password')
                ->attr('label', 'Пароль')
                ->attr('plh', 'Введите пароль')
                ->rule(':password', 'required');
//                ->rule('User\Validation::auth');
//                ->rule('min_lenth', 8);
        
        $form = Form\Form::factory('auth')
                ->attr('class', 'wn-form')
                ->attr('action', '/form/user-userform/auth')
                ->attr('name', 'auth')
                ->add($username)
                ->add($password)
                ->js("/media/js/form.js")
                ->funcSuccess(['User\Validation', 'auth']);
//                ->funcSuccess([__CLASS__, 'auth_success']);
        
        
        
        return $form;
        
    }

    public static function auth_success($post)
    {
        
    }
   
}
