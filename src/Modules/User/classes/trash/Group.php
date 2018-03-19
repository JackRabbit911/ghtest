<?php
namespace User;
/**
 * Description of Group
 *
 * @author JackRabbit
 */
use User\Model\Group as ModelGroup;
use Core\Helper\Arr;
//use User\AutoRole;

class Group
{
    public static $enable = TRUE;
    
    protected static $roles = [
            'root'      => 100,
            'owner'     => 80,
            'admin'     => 70,
            'manager'   => 50,
            'moderator' => 40,
            'author'    => 30,
            'member'    => 20,
            'auth'      => 10,
            'guest'     => 0,
        ];
    
    protected static $auto = [];
    
    protected $modelGroup = NULL;
    
    public function __construct($user_id)
    {
        $this->modelGroup = new ModelGroup($user_id);
        
//        var_dump($user_id);
    }
    
    public function group($group, $weight = NULL)
    {
//        if(!static::$modelGroup) static::$modelGroup = new ModelGroup($user->id);
        
        if(!is_numeric($weight)) $weight = Arr::get(static::$roles, $weight, 10);
        
        return ($this->modelGroup->get_group($group, $weight) === FALSE) ? FALSE : TRUE;
    }
    
    public function role($role, $group = NULL)
    {
//        $auto = $this->auto($group, $role);
        
//        if($auto !== NULL) return $auto;
        
//        if(!static::$modelGroup) static::$modelGroup = new ModelGroup($this->id);
        
        $weight = Arr::get(static::$roles, $role);
        
        if($weight === NULL) return FALSE;
        
        return ($this->modelGroup->get_role($weight, $group) === FALSE) ? FALSE : TRUE;
    }
    
    public function auto($group, $role)
    {
//        return AutoRole::instance($this)->$role();
        $weight = Arr::get(static::$auto, $group);
        
        if($weight === NULL) return NULL;
        
        retur (Arr::get(static::$roles, $role, 0) >= $weight) ? TRUE : FALSE;
    }
    
    public function add($group, $role)
    {
        if(empty($group)) return;
        
        if(is_numeric($role)) $weight = $role;
        else $weight = Arr::get(static::$roles, $role, 0);
        
        static::$auto[$group] = $weight;
    }
    
//    public function m($group)
//    {
//        if(!static::$modelGroup) static::$modelGroup = new ModelGroup($this->id);
//        return static::$modelGroup->get_group($group);
//    }
}
