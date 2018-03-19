<?php
namespace Core\Session;
/**
 * Description of Sqlite
 *
 * @author JackRabbit
 */

use Core\Session as Session;
use Core\Helper\Arr;

class Sql implements \SessionHandlerInterface
{
    protected $db;
    
    public function open($savePath=NULL, $sessionName='WNSID') {}

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $stmt = $this->db->prepare("SELECT data FROM sessions WHERE id=?");
        $stmt->execute(array($id));
        $return = $stmt->fetchColumn();
        
        $now = time();
        
        if($return)
        {
            $stmt = $this->db->prepare("UPDATE sessions SET last_activity = :last_activity WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':last_activity', $now);
            $stmt->execute();
        }
        
        return $return;
    }

    public function write($id, $data)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM sessions WHERE id=?");
        $stmt->execute(array($id));
        
        $now = time();
        
        if($stmt->fetchColumn() > 0)
        {
            $stmt = $this->db->prepare("UPDATE sessions SET last_activity = :last_activity, data = :data WHERE id = :id");
        }
        else
        {
            $stmt = $this->db->prepare("INSERT INTO sessions (id, last_activity, data) VALUES (:id, :last_activity, :data)");            
        }
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':last_activity', $now);
        $stmt->bindParam(':data', $data);

        $stmt->execute();
        
        return  true;
    }

    public function destroy($id)
    {
        if(!empty($this->db))
        {
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE id = ?");
            $stmt->execute(array($id));
        }
        return TRUE;
    }

    public function gc($lifetime)
    {
        $endtime = time() - $lifetime;
        if(!empty($this->db))
        {
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE last_activity < ?");
            $stmt->execute(array($endtime));
        }
        return TRUE;
    }
    
    public function last_activity()
    {
        $id = Arr::get($_COOKIE, Session::$name); 
        $stmt = $this->db->prepare("SELECT last_activity FROM sessions WHERE id=?");
        $stmt->execute(array($id));
        return $stmt->fetchColumn();
    }
    
    public function regenerate($strict = 1)
    {
        if($strict === 0) return;
        elseif($strict === 1)
        {
            $sid = session_id();
            $old_sid = Arr::get($_SESSION, 'old_sid');
            $this->destroy($old_sid);
            $_SESSION['old_sid'] = $sid;
            session_regenerate_id();
        }
        else session_regenerate_id(TRUE);
    }
    
    
}
