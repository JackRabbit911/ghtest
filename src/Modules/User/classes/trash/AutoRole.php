<?php
namespace User;
/**
 * Description of Acc
 *
 * @author JackRabbit
 */
//use User\Model\Acc as ModelAcc;
//use Core\Helper\Arr;

class AutoRole
{
    protected static $instance;
    protected $user;
    
    public static function instance(User $user)
    {        
        if(!(static::$instance instanceof static))
        {
            static::$instance = new static($user);
        }
        return static::$instance;
    }
    
    protected function __construct(User $user)
    {
        $this->user = &$user;
    }
    
    public function __call($name, $arguments)
    {
        return NULL;
    }
    
    public function auth()
    {
        return ($this->user->id) ? TRUE : FALSE;
    }
    
    public function guest()
    {
//        return $this->user->id;
        return ($this->user->id) ? FALSE : TRUE;
    }
}
