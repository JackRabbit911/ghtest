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
use Core\Pattern\DataIterator;
//use Core\Session;
//use User\Model\Auth as ModelAuth;


//use \User\Group;

class User extends DataIterator //implements Accble
{
//    use \User\Auth;
    
    
//    const ROOT = 100;
    
//    var $role;
//    public $group;

    protected static $instance;
    
//    protected static $model;
//    
//    protected $data = [];
    
       
    /**
     * Singletone pattern
     * @return object this class
     */
    public static function instance($id = FALSE)
    {        
        if(!(static::$instance instanceof static))
        {
            static::$instance = new static($id);
        }
        return static::$instance;
    }
    
    public static function auth($id = FALSE)
    {
        return static::instance($id);
    }
    
//    protected static function _auth()
//    {
//        $id = Session::instance()->get('user_id');
//        
//        if(!$id)
//            $id = Token::instance()->user_id();
//        
//        if(!empty($id))         
//            return static::$model->get($id);              
//        else return FALSE;
//    }
//    
//    protected static function force_login($user_id)
//    {       
//        $session = Session::instance();
//        $session->start()->set('user_id', $user_id);
//    }
    
    protected function __construct($id)
    {
//        if(static::$instance instanceof static)
//        {
//            static::$modelAuth = new ModelAuth($this);
//            $this->session = Session::instance();
            $this->data = Auth::regenerate($id);
//        }

            
//        if(Group::$enable)
        {
//            $this->acc = new Group($this->id);
//            $this->role = [$acc, 'role'];
//            $this->group = [$acc, 'group'];
        }
       
    }
    
    public function role($role, $group = NULL)
    {
//        return $this->acc->role($role, $group);
        return Roles::role($this->id, $role, $group);
    }
    
    public function group($group, $role = NULL)
    {
//        return $this->acc->group($group. $role);
        return Roles::group($this->id, $group, $role);
    }
        
//    public function regenerate($id = FALSE)
//    {
//        if($id !== FALSE)
//        {
//            static::force_login($id);
//        }
//        
//        if($userdata = static::_auth())
//        {
//            $this->data = (array) $userdata;
//            unset($userdata);
//        }
//        else
//        {
//            $this->data = [];
//            $this->data['id'] = FALSE;
//            $this->data['name'] = 'guest';
//        }
//    }
//    
    public function log_out()
    {
       Auth::log_out();
    }
    
    public function save()
    {
        static::$model->set($this);
    }
}
