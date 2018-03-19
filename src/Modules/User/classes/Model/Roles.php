<?php
namespace User\Model;
/**
 * Description of Auth
 *
 * @author JackRabbit
 */
use Core\DB;


class Roles
{
    public static $table = 'users_groups';
    public static $main_group = 'site';
    
//    protected $db;
//    protected $user_id = FALSE;
    
    
//    public function __construct($user_id)
//    {
//        $this->db = DB::instance();
//        
//        $this->user_id = $user_id;
//        
//        if(!$this->db->schema()->is_table(static::$table))
//        {
//            $this->db->create(static::$table)
//                    ->column('user_id', 'int(11)')
//                    ->column('group', 'varchar(255) null')
//                    ->column('weight', 'int(3)')
//                    ->execute();
//        }
//    }
    
    public static function get_role($user_id, $weight, $group = NULL)
    {
        $db = DB::instance()->select()
                ->from(static::$table)
                ->where('user_id', '=', $user_id);
        
//        return $db->render();
                
        
        if($weight >= 10) $compare = '>=';
        else $compare = '=';
        
        if($group !== FALSE)
        {
            $db->where('weight', $compare, $weight);
            
            if($group !== NULL)
            {
                $majors = static::_majors($group);
                $db->where('group', 'IN', $majors);
            }

//            $db->where('group', '=', NULL, 'OR')
//                    ->where('group', '=', '', ' )');
        }
        else
        {
            $db->where('weight', $compare, $weight);
        }
        
//        return $db->render();
        
        return $db->execute(DB::ROW);
    }
    
    public static function get_group($user_id, $group, $weight = NULL)
    {
        
        
        $db = DB::instance()->select('group')
                ->from(static::$table)
                ->where('user_id', '=', $user_id);
        
        if($weight >= 10) $compare = '>=';
        else $compare = '=';
        
        
        if($weight !== NULL) $db->where ('weight', $compare, $weight);
        
        $groups_db = $db->execute(DB::ALL);
        
        $users_groups = [];
        
        foreach($groups_db AS $item)
        {
            $majors = static::_majors($item->group);
            $users_groups = array_merge($users_groups, $majors);
        }
        
        $users_groups = array_unique($users_groups);
        
        return (in_array($group, $users_groups)) ? TRUE : FALSE;
    }


    protected static function _majors($group, $delimeter = '.')
    {
        $g = explode($delimeter, $group);
        $res = [];
        
        foreach($g AS $key=>$val)
        {
            if(isset($res[$key-1])) $add = $res[$key-1].$delimeter;
            else $add = NULL;
            $res[$key] = $add.$val;
        }
        
        array_unshift($res, static::$main_group);
        
        return $res;
    }
    
    
}
