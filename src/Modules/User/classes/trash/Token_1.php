<?php
namespace User;
/**
 * Description of Form
 *
 * @author JackRabbit
 */
//use Core\Session;
use Core\DB;
use Core\HTTP;
use Core\Helper\Text;

class Token
{
    public static $cookie_name = 'WNTOKEN';
    public static $connect = [
            'driver'    => 'sqlite',
            'path'      => 'App/sessions',
            'dbname'    => 'tokens.sdb',
        ];
    public static $table_name = 'tokens';
    
    protected static $instance;
    
    protected static $columns = [
            'token'     => 'VARCHAR(32) NOT NULL PRIMARY KEY UNIQUE',
            'user_id'   => 'INTEGER',
            'user_agent'=> 'TEXT',
            'last_activity'=> 'INTEGER',
        ];
    
    protected $db;
    
    public static function instance()
    {        
        if(!(static::$instance instanceof static))
        {
            static::$instance = new static();
        }
        return static::$instance;
    }
    
    protected function __construct()
    {
        $this->db = DB::instance(static::$connect);
        
        if(!$this->db->schema()->is_table(static::$table_name))
        {
            $this->db->create(static::$table_name)->columns(static::$columns)->execute();
        }
    }

    public function get()
    {
        $token = filter_input(INPUT_COOKIE, static::$cookie_name, FILTER_SANITIZE_SPECIAL_CHARS);
        
//        return $token;
        
        if($token)
        {
            $row = $this->db->table(static::$table_name)->get($token, 'token');
            
//            return $row;
            
            if($row)
            {
                if($row->user_agent === HTTP::user_agent())
                {
                    $this->refresh($row->user_id, $token);
                    return $row;
                }
                else $this->delete();
            }
            else $this->delete();
        }
                
        return FALSE;
    }
    
    public function set($user_id)
    {        
        $row_roken = $this->get();
        
        if(!empty($row_token))
        {
            $token = $row_roken->token;
        }
        else $token = NULL;
        
        return $this->refresh($user_id, $token);
    }
    
    protected function refresh($user_id, $token=NULL)
    {
        $new_token = $this->create();
        $data = [
            'token'=>$new_token, 
            'user_id'=>$user_id, 
            'user_agent'=>HTTP::user_agent(), 
            'last_activity'=>time()
            ];
        
        if($token)
            $this->db->table(static::$table_name)->delete($token, 'token');
        
        $this->db->table(static::$table_name)->set($data, 'token');
        setcookie(static::$cookie_name, $new_token, time()+3600, '/');
        
        return $new_token;
    }
    
    protected function create()
    {
        function _is_token_unique($token, $db)
        {
            $array = $db->table('tokens')->getAll($token, 'token');
            return (count($array) === 0) ? TRUE : FALSE;
        }

        do
        {
            $token = sha1(uniqid(Text::random('alnum', 32), TRUE));
        }
        while(!_is_token_unique($token, $this->db));
        return $token;
    }

    public function delete()
    {
        if($this->db->schema()->is_table(static::$table_name))
        {
            $token = filter_input(INPUT_COOKIE, static::$cookie_name, FILTER_SANITIZE_SPECIAL_CHARS);
            $this->db->table(static::$table_name)->delete($token, 'token');
        }
        setcookie(static::$cookie_name, "", -100, '/');
    }
    
}
