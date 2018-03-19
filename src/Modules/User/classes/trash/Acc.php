<?php
namespace User\Model;
/**
 * Description of Auth
 *
 * @author JackRabbit
 */
use Core\DB;

class Acc
{
    public static $table_roles = 'roles';
    public static $table_users_roles = 'users_roles';    
    
    protected static $roles = [
            'root'      => 100,
            'owner'     => 80,
            'admin'     => 70,
            'manager'   => 50,
            'moderator' => 40,
            'author'    => 30,
            'auth'      => 10,
            'guest'     => 0,
        ];
    
    protected $db;
    protected $user_id = FALSE;
    
    protected static $users_roles = [];
    
    protected $to_up = [];
    protected $to_right = [];
    protected $to_left;
    
    public function __construct($user_id)
    {
        $this->db = DB::instance();
        
        $this->user_id = $user_id;
        
        if(!$this->db->schema()->is_table(static::$table_users_roles))
        {
            $this->db->create(static::$table_users_roles)
                    ->column('user_id', 'int(11)')
                    ->column('role', 'varchar(255)')
                    ->execute();
        }
    }
    
    public function get()
    {
        if($this->user_id === FALSE) return ['guest'];
        
        $rows = $this->db->table(static::$table_users_roles)->getAll($this->user_id, 'user_id');
        
        $users_roles = [];
        foreach($rows AS $row)
        {
            $users_roles[] = $row->role;
        }
        
        $users_roles[] = 'auth';
        
        return $users_roles;
    }
    
    public function set($user_id, $role)
    {
        return $this->db->table(static::$table_users_roles)
                ->set(['user_id'=>$user_id, 'role'=>$role], 'user_id');   
    }
    
    public function is_rights($user_id, $role)
    {
       $users_roles = $this->get($user_id);
       
//       echo '<br>'.$user_id.'<br>';
//       
//       var_dump($users_roles); exit;
       
       $roles = [];
       foreach($users_roles AS $item)
       {
           $roles[] = $item->role;
       }
       
       $group = $this->_roles($role);
       
       return (empty(array_intersect($group, $roles))) ? FALSE : TRUE;
    }
    
    public function in_group($user_id, $role)
    {
        $users_roles = $this->get($user_id);
        $minors = $this->_minors($role);
        
        $users_roles += $minors;
        
        return $users_roles;
    }
    
    public function get_like($user_id, $role)
    {
        $majors = $this->_majors($role);
        
        $x = $this->db->select('role')->from(static::$table_users_roles)
                ->where('user_id', '=', $user_id, 'AND (')
                ->where('role', 'LIKE', $role.'%', 'OR')
                ->where('role', 'IN', $majors, ')')
//                ->render();
                ->execute(DB::ALL);
        
        return $x;
        
//        if(!empty($majors))
//        {
//            $x->where('role', 'IN', $majors);
//        }
//        
//                $y = $x->execute(DB::ALL);
////                ->render();
//  
//                
//        return $y;
    }
    
    public function _majors($role)
    {
        $g = explode('.', $role);
        
        $r = array_shift($g);
        
        echo $r;
        echo '<br>';
        
        $res = [];
        foreach(static::$roles AS $key=>$value)
        {
            if($value >= static::$roles[$r])
                $res[] = $key;
        }
        
        var_dump($res);
        
        return $res;
        
//        return array_merge($res, $group);
    }
    
    public function _minors($role)
    {
        $g = explode('.', $role);
        
        $r = array_shift($g);
        
        echo static::$roles[$r];  //$r;
        echo '<br>';
        
        $res = [];
        foreach(static::$roles AS $key=>$value)
        {
            if($value <= 50 && $value > 10)
                $res[] = $key;
        }
        
//        var_dump($res);
        
        return $res;
        
//        return array_merge($res, $group);
    }
    
    public function set_role($role)
    {
        if($role === 'guest')
        {
            static::$users_roles = [];            
        }
        
        static::$users_roles[] = $role;
    }


    public function _roles($role)
    {
        if(in_array($role, static::$users_roles)) return TRUE;
        
        
        $roles = $this->_get_roles_list();
        
        $res = $this->_get_roles_for_search($role, $roles);
        
        return $res;
        
        
        
//        $g = explode('.', $role);
//        $r = array_shift($g);
//        
//        if(!isset(static::$roles[$r])) return FALSE;
//        
//        $str = array_shift($g);
//        
//        if($str)
//            $this->to_right[] = $str;
//        
//        foreach($g AS $key=>$item)
//        {            
//            $str .= '.'.$item;
//            $this->to_right[] = $str;           
//        }
//        
//        foreach(static::$roles AS $key=>$value)
//        {
//            if($value >= static::$roles[$r])
//            {
//                $this->to_up[] = $key;
//                
//                foreach($this->to_right AS $segment)
//                {
//                    $this->to_up[] = $key.'.'.$segment;
//                }
//            }
//        }
//        
//        return $this->to_up;
        
//        return array_merge($res, $group);
    }
    
    protected function _get_roles_list($source = NULL)
    {
        if($source === NULL) return static::$roles;
    }
    
    protected function _get_roles_for_search($role, $roles)
    {
        $to_right = $search = [];
        
        $g = explode('.', $role);
        $r = array_shift($g);
        
        if(!isset($roles[$r])) return FALSE;
        
        $str = array_shift($g);
        
        if($str)
            $to_right[] = $str;
        
        foreach($g AS $key=>$item)
        {            
            $str .= '.'.$item;
            $to_right[] = $str;           
        }
        
        foreach(static::$roles AS $key=>$value)
        {
            if($value >= static::$roles[$r])
            {
                $search[] = $key;
                
                foreach($to_right AS $segment)
                {
                    $search[] = $key.'.'.$segment;
                }
            }
        }
        
        return $search;
    }
}
