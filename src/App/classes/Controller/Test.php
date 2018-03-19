<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

use Core\Controller;
//use Core\Route;
use Core\Core;
use Core\Route;
//use Core\Exception\Exception;
//use Core\HTTP;
//use Core\Cache;
use Core\Helper\Arr;
use Core\Session;
//use Core\DB;
use Form;
use Core\Validation;
use Core\I18n;
use Core\Request;

use User\Auth;
use User\User;
use User\Token;
use User\Acc;


use Core\HTTP;

use App\Model;
use Core\DB;
use Core\Helper\Text;
use Core\Helper\Cookie;
use Core\Helper\Headers;
use Core\Response;
/**
 * Description of Index
 *
 * @author JackRabbit
 */
class Test extends Controller\Controller
{
    public function arr()
    {
//        $arr1 = ['первый', 'второй'];
//        $arr2 = ['третий', 'второй'];
//        
//        print_r(Arr::merge($arr1, $arr2));
//        echo '<br>';
//        print_r(array_replace($arr1, $arr2));
        
        $post = ['key1'=>'val1', 'key2'=>'val2'];
        $response = ['key1'=>['msg'=>'lala', 'status'=>'success'], 'key2'=>['msg'=>'lala', 'status'=>'success']];
        
        call_user_func_array([$this, '_func'], [$post, $response]);
        
    }
    
    private function _func($post, $response)
    {
        echo json_encode($response);
    }

    public function index()
    {
//        echo \Core\Route::$current;
        
        phpinfo();
        
        echo 'PHP version '.phpversion();
        echo '<br>';
        echo 'Apache version '.apache_get_version();
        echo '<br>';
        echo 'Memory size '.memory_get_usage();
        echo '<br>';
//        print_r(get_loaded_extensions());
//        echo mysqli_get_server_info();
        
//        echo \PDO::getAttribute(PDO::ATTR_SERVER_VERSION);
        
        if(extension_loaded('gd')) echo 'lib gd is loaded'.'<br>';
        if(extension_loaded('pdo_mysql')) echo 'lib pdo_mysql is loaded'.'<br>';
        if(extension_loaded('pdo_sqlite')) echo 'lib pdo_sqlite is loaded'.'<br>';
        if(extension_loaded('sqlite3')) echo 'lib sqlite3 is loaded'.'<br>';
        
        if(class_exists('\Memcache'))
        {
           echo 'memcache is enabled'.'<br>';
        }
        
        if(extension_loaded('redis')) echo 'lib redis is loaded'.'<br>';
        if(extension_loaded('mongodb')) echo 'lib mongodb is loaded'.'<br>';
        
    }
    
    public function patt()
    {
        var_dump(Route::all());
        exit;
        
//        Acc::setting();
        
//        Session::gc();
        
//        $user = User::factory(1);
        
        $user = Auth::instance();
        
        $user->log_out();
        
//        $role = 'admin'; // 'admin.pages.seo';
        
//        $user->log_out();
        
//        return;
        
        foreach($user AS $key=>$item)
        {
            echo $key.' - ';
            echo $item;
            echo '<br>';
        }
        
        var_dump($user->group('auth'));
        
//        $user->avatar = 'porutchik';
        
        $u = User::factory(7);
        
        echo '<hr>';
        
        var_dump($u);
        
        echo '<hr>';
        
//        $u->name = 'blondinka';
//        $u->pass = '1111';
//        $u->email = 'bl@zzz.qq';
//        $u->avatar = 'photo';
//        var_dump($u);
        
//        var_dump($u->save());
        
//        echo $user->pass;
        echo '<hr>';
        
        $list = User::getAll();
        
//        var_dump($list);
        
        foreach($list AS $item)
        {
            echo $item->name.' - '.$item->pass.'<br>';
        }
        
//        $user->save();
        
//        $user->avatar = new \stdClass();
//        $user->avatar->qq = 'ava';
//        $user->next();
//        $x = $user->key();
//        $y = $user->current(); 
        
//        $g = 'seo.a.b';
        
//        var_dump($user->role('auth', 'seo.a'));
        
//        echo 'group '.$g.' - '; 
//        var_dump($user->acc->group($g, 'admin'));
//        echo '<br>';
//        var_dump($user->group('seo.a', 'member'));
//        echo 'role in '.$g.' - '; var_dump($user->acc->role('admin', $g));
//        echo '<br>';
//        var_dump($user->m('seo.c'));
//        var_dump($user->m('seo'));
//        
//        echo '<br>';
//        
////        $user->set_roles('qqq', 'guest.ban');
//        
//        var_dump($user->roles());
//        
//        echo '<br>';
//        
//        foreach ($user AS $prop)
//        {
//            echo $prop;
//            echo '<br>';
//        }
        
//        $y = $user->acc($role);
//         
//        var_dump($y);
                
        
//        $model = new \User\Model\Roles;
//        $model->set(3, 'admin.pages');
        
//        var_dump($user->role('admin'));
//        echo '<br>';
//        var_dump($user::ROOT);
    }
    
    public function dbb()
    {
        
        
        $token = Token::instance('mysql');
//        
//        $q = $token->gc();
//        
//        var_dump($q);
        
        var_dump($token->get()); 
        
//        $token->set(1);
        
//        $token->delete();
        
         echo '<hr>';
        
        var_dump($token->get()); 
//        var_dump($t);
        
//        $user = User::instance();
        
//        var_dump(Session::$handler);
        
//        Session::gc(300);
        
//        Cookie::delete('WNELSE');
        
        
//        setcookie('WNELSE', 'test');
        
//        Session::instance()->gc(120);
        
//        echo $user->id;
        
//        $t = Token::instance()->set($user->id);
//        $t = Token::instance()->get();
        
//        $t = Token::instance()->create();
//        
//        $i = 0;
//        do
//        {
//            $t = sha1(uniqid(Text::random('alnum', 32), TRUE));
//            $i++;
//        }
//        while(false);
        
//        var_dump($t);
        
//        $user->log_out();
        
//        var_dump(Cookie::$cookies);
        
//        var_dump($user);
//        
//        $user->regenerate(3);
        
//        $connect = [
//            'driver'    => 'sqlite',
//            'path'      => 'App/sessions',
//            'dbname'    => 'tokens.sdb',
//        ];
//        $db = DB::instance($connect);
//        
//        $data = [
////            'id'    => 1,
////            'name'  => 'John',
////            'email' => 'john@qq.qq',
////            'pass'  => 'Doe',
//            'token' => 'qwerty1',
//            'user_id'=>5
//        ];
//        
//        $x = $db->table('tokens')->set($data, 'token');
//        
////        $user = $db->select()->from('users')->where('id', '=', 4)->execute(DB::ALL);
//
//        
        echo '<hr>';
//        var_dump($_COOKIE);
//        echo '<hr>';
//        var_dump(Session::instance()->get());
//        echo '<br>';
//        
//        
//        $users = $db->table('tokens')->getAll();
//        
//        var_dump($users);
        
//        foreach($user AS $key=>$val)
//        {
//            echo $key.' - '.$val.'<br>';
//        }
        
//        var_dump(Cookie::is_sent('WNSID'));
//        
        echo '<br>';
//        . session_name();
        
        
//        $header = 'Set-Cookie: WNSID=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=0; path=/';
//        
//        var_dump(in_array($header, headers_list()));
        
//        var_dump(headers_list());
        
//        var_dump(Response::headers());
        
//        $path = 'set-cookie.wnsid';
        
//        echo '<hr>';
//        
//        var_dump(headers_list());
//        
//        echo '<hr>';
//        
//        var_dump(Headers::get('set-cookie'));
//        
//        echo '<hr>';
        
//        var_dump(Headers::is_sent('set-cookie', 'WNT=deleted'));
//        var_dump(Headers::is_sent('set-cookie.lala'));
//        var_dump(Cookie::is_sent('WNT', 'deleted'));
        
//        echo Headers::set('location', 'http://wn.test');
        
//        var_dump(Cookie::is_sent($path));
        
//        print_r(apache_response_headers());
//        var_dump($_COOKIE);
        
//        print_r(Session::instance()->get('user_id'));
    }


    public function sess()
    {
//        $settings = Core::config('sessions');
        
        
//        $session = Session::instance();
//        
//        var_dump($session->session_id());
        echo Text::random('alnum', 32);
        
//        $sid = sha1(uniqid(Text::random('alnum', 32), TRUE)); // md5(uniqid(rand(), true)); //sha1(uniqid('WNDIS'));
//        
//        var_dump($sid);
    }
    
    public function valid()
    {
        $rules = [
            'username'  => ['required', ':username'],
            'password'  => [':password', 'User\Validation::auth'],
//            'confirm'   => ['confirm'],
//            'date'      => ':date',
//            'num'       => ['filter', 257, ['options'=>['min_range'=>5, 'max_range'=>10]]],
//            'email'     => [':email', 'User\Validation::is_unique', 'email'],
////            'num'       => ['filter', 257],
//            'bar'       => ['lenth', 4, 6],
//            'min_l'     => ['min_lenth', 7],
//            'max_l'     => ['max_lenth', 6],
        ];
        
        $v = new Validation($rules);
        
//        print_r($v->_rules);
//        exit;
        
        $post = [
            'username'  => 'porutchik',
            'password'  => '0110671',
//            'confirm'   => '0110671',
//            'date'      => '31.06.1980',
//            'num'       => '12',
//            'foo'       => 'bar',
//            'email'     => 'ww@qq.qq',
//            'bar'       => 'foobar',
//            'min_l'     => 'foobar',
//            'max_l'     => 'foofoobar'
        ];
        
        
//        if($v->check($post))
//        {
//            echo 'OK';
//        }
//        else
//        {
//            foreach($v->response AS $name => $item)
//            {
//                if($item['status'] === 'error')
//                {
//                    $v->response[$name]['msg'] = $v->message($name);
////                    
//                    var_dump($v->response[$name]['msg']);
////                    echo '<br>';
////                    var_dump($v->replace[$name]);
//                    echo '<br>';
//                    echo $v->response[$name]['code'];
//                    echo '<br>';
//                    var_dump($v->replace[$name]);
//                    echo '<hr>';
//                }
//            }
//        }
        
//        $ok = $v->check($post);
        
        $ok = $v->checkField('username', 'porutchik');
        
        $ok = $v->checkField('password', '011067');
        
//        $ok = $v->checkField('confirm', '011067');
        
        var_dump($ok);
        
        
        echo '<br>';
        
        var_dump($v->response);
        
        echo '<br>';
//        
//        var_dump($v->message('password'));
//        
//        echo '<br>';
    }
    
    public function form()
    {


//        $form_login = Request::internal('/user/auth')->execute();
        
//        $username = Form\Input::factory('username')
//                ->addAttr('type', 'text')
//                ->addAttr('label', 'Имя или email')
//                ->addAttr('plh', 'Введите имя или email')
//                ->addAttr('action', 'test/form');
//        
//        $password = Form\Input::factory('password')
//                ->addAttr('type', 'password')
//                ->addAttr('label', 'Пароль')
//                ->addAttr('plh', 'Введите пароль');
//        
////        var_dump($password); exit;
//        
//        $form = Form\Form::factory()
//                ->addAttr('class', 'wn-form')
//                ->addAttr('action', '/test/form')
////                ->addAttr('id', 'wn-form')
//                ->addAttr('name', 'auth')
//                ->add($username)
//                ->add($password)
//                ->funcSuccess([$this, '_success']);
                
//        var_dump($form_login); exit;
        
        
//        var_dump($this->request->post());
        
//        $form = $form->action($this->request->post());
        
        
//        $form = Form\Form::execute('User\Form\User', 'auth');
        
        
//        $form = call_user_func(['User\Userform', 'auth']);  
        
//        $v = new Validation();
//        $v->rules(['username' => ['required', ':username', ['min_lenth', 3]]]);
//        $v->rule('password', ':password');
//        $v->rule('password', ['min_lenth', 8]);
        
        
        
//        print_r($v->_rules);
//        var_dump($v->check(['password'=>'']));
//        echo '<br>';
        
        $form = Request::internal('/form/user-userform/auth')->execute();
        
              
//        print_r($form->_rules);
        
//        var_dump($v->check(['password'=>'']));
        
//        var_dump($form->validation(['password'=>'']));
        
        echo $this->_view('test/wrap')
                ->set('form_login', $form);
//                ->js("/media/js/form.js");
        
    }
    
    public function _success($post, $form)
    {
        $username = $form->fields['username'];
        
        $arr_class = $username->addAttr('class');       
        array_push($arr_class, 'has-success', 'has-feedback');
        
        $username->addAttr('class', $arr_class);
        $username->addAttr('value', $post['username']);
        
        return $form;
    }
    
    public static function myfunc($value, $response)
    {
//        var_dump($response); exit;
        
        $response->code = __FUNCTION__;
        $response->msg = 'АШЫПКО! '.$value;
        $response->status = 'warning';
        return FALSE;
    }
    
}
