<?php
namespace User\Model;
/**
 * Description of Auth
 *
 * @author JackRabbit
 */
use Core\DB;


class Group
{
    public static $table = 'users_groups';   
    
//    protected static $roles = [
//            'root'      => 100,
//            'owner'     => 80,
//            'admin'     => 70,
//            'manager'   => 50,
//            'moderator' => 40,
//            'author'    => 30,
//            'auth'      => 10,
//            'guest'     => 0,
//        ];
    
    protected $db;
    protected $user_id = FALSE;
    
    
    public function __construct($user_id)
    {
        $this->db = DB::instance();
        
        $this->user_id = $user_id;
        
        if(!$this->db->schema()->is_table(static::$table))
        {
            $this->db->create(static::$table)
                    ->column('user_id', 'int(11)')
                    ->column('group', 'varchar(255) null')
                    ->column('weight', 'int(3)')
                    ->execute();
        }
    }
    
    public function get_role($weight, $group = NULL)
    {
        $db = $this->db->select()
                ->from(static::$table)
                ->where('user_id', '=', $this->user_id);
                
        
        if($group !== FALSE)
        {
            $db->where('weight', '>=', $weight, 'AND (');
            
            if($group !== NULL)
            {
                $majors = $this->_majors($group);
                $db->where('group', 'IN', $majors, 'OR');
            }

            $db->where('group', '=', NULL, 'OR')
                    ->where('group', '=', '', ' )');
        }
        else
        {
            $db->where('weight', '>=', $weight);
        }
        
        return $db->execute(DB::ROW);
    }
    
    public function get_group($group, $weight = NULL)
    {
        $db = $this->db->select('group')
                ->from(static::$table)
                ->where('user_id', '=', $this->user_id);
        
        if($weight) $db->where ('weight', '>=', $weight);
        
        $groups_db = $db->execute(DB::ALL);
        
        $users_groups = [];
        
        foreach($groups_db AS $item)
        {
            $majors = $this->_majors($item->group);
            $users_groups = array_merge($users_groups, $majors);
        }
        
        $users_groups = array_unique($users_groups);
        
        return (in_array($group, $users_groups)) ? TRUE : FALSE;
    }


    protected function _majors($group, $delimeter = '.')
    {
        $g = explode($delimeter, $group);
        $res = [];
        
        foreach($g AS $key=>$val)
        {
            if(isset($res[$key-1])) $add = $res[$key-1].$delimeter;
            else $add = NULL;
            $res[$key] = $add.$val;
        }
        
        return $res;
    }
   
}
