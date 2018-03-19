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

class Roles
{
//    public static $enable = TRUE;
    
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
    
//    protected $modelGroup = NULL;
//    
//    public function __construct($user_id)
//    {
//        $this->modelGroup = new ModelGroup($user_id);
//        
////        var_dump($user_id);
//    }
    
    public static function group($user_id, $group, $role)
    {
        
//        return static::$auto;
//        if(!static::$modelGroup) static::$modelGroup = new ModelGroup($user->id);
        
//        if(!is_numeric($weight)) 
            $weight = Arr::get(static::$roles, $role, 10);
//        if($weight === NULL) return FALSE;
        
        $auto = static::auto($group, $weight);        
        if($auto === TRUE) return TRUE;
        
        
        return (Model\Roles::get_group($user_id, $group, $weight) === FALSE) ? FALSE : TRUE;
    }
    
    public static function role($user_id, $role, $group = 'site')
    {
        
//        return static::$auto;
        
//        return 'huy';
        
//        $auto = static::auto($group, $role);        
//        if($auto !== NULL) return $auto;
        
//        if(!static::$modelGroup) static::$modelGroup = new ModelGroup($this->id);
        
        $weight = Arr::get(static::$roles, $role);
        
        $auto = static::auto($group, $weight);
        
//        return $group.' - '.$weight;
        
        
        if($auto === TRUE) return TRUE;
        
//        return Model\Roles::get_role($user_id, $weight, $group);
        
        if($weight === NULL) return FALSE;
        
        return (Model\Roles::get_role($user_id, $weight, $group) === FALSE) ? FALSE : TRUE;
    }
    
    public static function auto($group, $weight)
    {
        
        
        
        
        
//        return AutoRole::instance($this)->$role();
//        $weight = Arr::get(static::$auto, $group);
        
//        if($weight === NULL) return NULL;
        
//        var_dump($weight);
        
//        return static::$roles;
        
        if(empty($group)) $group = Model\Roles::$main_group;
        
        $auto = Arr::get(static::$auto, $group);
        
       
        
        if($auto === NULL) return FALSE;
        
//        var_dump($auto);
//        var_dump($weight);
//        var_dump(Model\Roles::$main_group);
//        var_dump(Arr::get(static::$roles, Model\Roles::$main_group, 100));
        
        if($weight >= Arr::get(static::$roles, 'auth', 10))
        {
            return ($auto >= $weight) ? TRUE : FALSE;
        }
        else
        {
            return ($auto = $weight) ? TRUE : FALSE;
        }
        
        
        
        
        
        
    }
    
    public static function setRole($role, $group = NULL)
    {
        if(empty($group)) $group = Model\Roles::$main_group;
        
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
