<?php
namespace User\Model;
/**
 * Description of Auth
 *
 * @author JackRabbit
 */
use Core\DB;
//use Core\Session;

class User
{
    public static $connect; // = 'mysql';
    public static $table = 'users';
    
//    protected $user;
    
//    public function __construct($user)
//    {
//        $this->user = &$user;
//        $this->db = DB::instance(static::$connect);
//    }
    
    public static function log_in($name_or_email, $password)
    {
        $obj = DB::instance(static::$connect)->select('id')
                ->from(static::$table)
                ->where_open()
                ->where('name', '=', $name_or_email)                
                ->or_where('email', '=', $name_or_email)
                ->where_close()
                ->and_where('pass', '=', $password)
//                ;
                ->execute(DB::ROW);
        
//        var_dump($obj->render()); exit;
        
        return ($obj) ? $obj->id : FALSE;               
    }
    
    public static function get($id)
    {
        $data = DB::instance(static::$connect)->table(static::$table)
                ->get($id);
        
//        return $data;
        
        return ($data) ? (array) $data : FALSE;
    }
    
    public static function getAll()
    {
        return DB::instance(static::$connect)->table(static::$table)
                ->getAll();
    }


    public static function set($data)
    {
        $db = DB::instance(static::$connect);
        
        $columns = $db->schema()->columns(static::$table);
        
        $props = [];
        
        foreach($data AS $key=>$value)
        {
            if(!in_array($key, $columns))
            {
                $props[$key] = $value;
                unset($data[$key]);
            }
        }
        
//        $data = (array)$data;
//        return DB::instance(static::$connect)->table(static::$table)->set($data);
        return $db->table(static::$table)->set($data);
    }
    
    public static function is_unique($field, $value)
    {
//        $res = DB::instance(static::$connect)
//                ->table(static::$table)
//                ->getAll($value, $field);
//        
//        if(empty($res))
//            return TRUE;
//        else
//        {
//            if(!$name) $name = $field;
//            $_valid->response[$name]['code'] = $field.'-unique';
//            return FALSE;
//        }
        
        return DB::instance(static::$connect)
                ->table(static::$table)
                ->is_unique($field, $value);
        
    }
}
