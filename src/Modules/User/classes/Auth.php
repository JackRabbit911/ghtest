<?php
namespace User;
/**
 * Description of Form
 *
 * @author JackRabbit
 */
//use Core\Pattern\DataIterator;
use Core\Session;
//use User\Model\Auth as Model;

class Auth extends UserAbstract //DataIterator
{
    protected static $instance;
    
//    protected $data = [];
    
    public static function instance($id = FALSE)
    {        
        if(!(static::$instance instanceof static))
        {
            static::$instance = new static($id);
        }
        return static::$instance;
    }
    
    protected function __construct($id)
    {
        $this->regenerate($id);
    }
    
    protected function _auth()
    {
        $id = Session::instance()->get('user_id');
        
        if(!$id)
            $id = Token::instance()->user_id();
        
        if(!empty($id))         
            return Model\User::get($id);              
        else return FALSE;
    }

    protected static function force_login($user_id)
    {       
        $session = Session::instance();
        $session->start()->set('user_id', $user_id);
    }
    
    public function regenerate($id = FALSE)
    {
        if($id !== FALSE)
        {
            static::force_login($id);
        }
        
        if($this->data = $this->_auth())
        {
            
//            var_dump($this->data);
            
            Roles::setRole('auth');
            Roles::setRole('auth', 'auth');
        }
        else
        {
            $this->data = [];
            $this->data['id'] = FALSE;
            $this->data['name'] = 'guest';
            
            Roles::setRole('guest');
        }
    }
    
    public function role($role, $group = NULL)
    {
        return Roles::role($this->id, $role, $group);
    }
    
    public function group($group, $role = NULL)
    {
        return Roles::group($this->id, $group, $role);
    }
    
    public function log_out()
    {
        Token::instance()->delete();
        Session::instance()->destroy();
        $this->regenerate();
    }
}
