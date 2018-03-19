<?php
namespace User;
/**
 * Description of Form
 *
 * @author JackRabbit
 */
use Core\Session;
//use User\Model\Auth as Model;

trait Auth
{
//    protected static $modelAuth;
    
//    protected $data = [];
    
    protected static function _auth()
    {
        $id = Session::instance()->get('user_id');
        
        if(!$id)
            $id = Token::instance()->user_id();
        
        if(!empty($id))         
            return Model\Auth::get($id);              
        else return FALSE;
    }
    
    protected static function force_login($user_id)
    {       
        $session = Session::instance();
        $session->start()->set('user_id', $user_id);
    }
    
    public static function regenerate($id = FALSE)
    {
        if($id !== FALSE)
        {
            static::force_login($id);
        }
        
        if($userdata = static::_auth())
        {
            $this->data = (array) $userdata;
            unset($userdata);
            
            Roles::setRole('auth');
            Roles::setRole('auth', 'auth');
//            $this->set_roles('user');
        }
        else
        {
            $this->data = [];
            $this->data['id'] = FALSE;
            $this->data['name'] = 'guest';
            
            Roles::setRole('guest');
            
//            $this->set_roles('guest');
        }
    }
    
    public function log_out()
    {
        Token::instance()->delete();
        Session::instance()->destroy();
        $this->regenerate();
//        $this->regenerate();
//        Session::instance()->destroy();
    }
}
