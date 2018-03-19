<?php
namespace Core\Session;
/**
 * Description of Sqlite
 *
 * @author JackRabbit
 */

use Core\Session as Session;

class Sqlite extends Sql
{    
    public function open($savePath=NULL, $sessionName='WNSID')
    {
        Session::mkdir();
        
        $this->db = new \PDO('sqlite:'.Session::$save_path.'sessions.sdb');
        
        $this->db->exec('CREATE TABLE IF NOT EXISTS sessions (id VARCHAR(32) NOT NULL PRIMARY KEY UNIQUE, last_activity INTEGER, data TEXT)');
        
        return true;
    }
   
}
