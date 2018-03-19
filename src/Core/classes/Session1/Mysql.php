<?php
namespace Core\Session;
/**
 * Description of Sqlite
 *
 * @author JackRabbit
 */

use Core\DB;

class Mysql extends Sql
{    
    public function open($savePath=NULL, $sessionName='WNSID')
    {
//        $connect = Core::config('connect', 'mysql');
//        
//        $dsn = 'mysql:dbname='.$connect['dbname'].';host='.$connect['host'];
//        
//        $this->db = new\PDO($dsn, $connect['username'], $connect['password']);
        
        
        $db = DB::instance('mysql');
        
//        var_dump($this->db);
        $this->db = $db->pdo;
        
        $x = $this->db->exec('CREATE TABLE IF NOT EXISTS sessions (id VARCHAR(32) NOT NULL PRIMARY KEY UNIQUE, last_activity INT(10), data VARCHAR(255))');
        
//        var_dump($x); exit;
        
        return true;
    }
   
}
