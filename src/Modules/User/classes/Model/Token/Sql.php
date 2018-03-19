<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace User\Model\Token;

/**
 * Description of Sqlite
 *
 * @author JackRabbit
 */
use User\Model\Token\TokenInterface;
use Core\DB;

abstract class Sql implements TokenInterface
{
   
    public static $table_name = 'tokens';
    
    protected static $columns = [
        'token'     => 'VARCHAR(40) NOT NULL PRIMARY KEY UNIQUE',
        'user_id'   => 'INT(11)',
        'user_agent'=> 'VARCHAR(128)',
        'last_activity'=> 'INT(10)',
    ];
    
    protected $db;
    
    protected $token;

    public function __construct()
    {
        $this->db = DB::instance(static::$connect);
        if(!$this->db->schema()->is_table(static::$table_name))
        {
            $this->db->create(static::$table_name)->columns(static::$columns)->execute();
        }
    }
    
    public function get($token)
    {
        $this->_token($token);
        
        return ($t = $this->db->table(static::$table_name)->get($this->token, 'token')) ? $t : NULL;
    }
    
    public function set($data, $token)
    {
        $this->_token($token);
        
        $cond = ['token'=>$token];
        
        $this->db->table(static::$table_name)->set($data, $cond);
        
        $this->token = $data['token'];
    }
    
    public function delete($token)
    {
        $this->_token($token);
        $this->db->table(static::$table_name)->delete($this->token, 'token');
    }
    
    public function is_unique($token)
    {  
        $array = $this->db->table(static::$table_name)->getAll($token, 'token');
        return (count($array) === 0) ? TRUE : FALSE;
    }
    
    public function gc($lifetime)
    {
        $endtime = time() - $lifetime;
        return $this->db->delete(static::$table_name)->where('last_activity', '<', $endtime)->execute();
    }
    
    protected function _token($token)
    {
        if(!$this->token) $this->token = $token;
    }
}
