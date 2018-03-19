<?php
namespace Core\DB;

/**
 * Description of Set
 *
 * @author JackRabbit
 */

use Core\DB as DB;
use Core\Helper\Arr;

class Table
{
    use Traits\Where;
    use Traits\Exec;
    
    protected $db;
    protected $table;
    
    public function __construct($db, $table)
    {      
        $this->db = $db;
        $this->table = $table;  
    }
    
    public function get($id = NULL, $column = 'id')
    {
        return $this->_get($id, $column, DB::ROW);
    }
    
    public function getAll($id = NULL, $column = 'id')
    {
        return $this->_get($id, $column, DB::ALL);
    }
   
    public function set(array $data, $cond='id')
    {
        if(is_array($cond))
        {
            if(!empty($cond))
            {
                $column = key($cond);
                $value = $cond[$column];
            }
            else $column = 'id';
        }
        else
        {
            $column = $cond;
            $value = Arr::get($data, $column);
        }
        
        unset($cond);
        
        if(!empty($value))
            $result = $this->db->select($column)->from($this->table)->where($column, '=', $value)->execute(DB::ALL);
        else $result = [];
        
        if(empty($value) || count($result) === 0)
            return $this->db->insert($this->table)->set($data)->execute();
        else
        {
            $this->db->update($this->table)->set($data)->where($column, '=', $value)->execute();
            return $data[$column];
        }
    }
    
    public function delete($id, $column = 'id')
    {
        return $this->db->delete($this->table)->where($column, '=', $id)->execute();
    }
    
    public function truncate()
    {
        $class = __NAMESPACE__.'\Drivers\\'.ucfirst($this->db->driver);
        
        $driver = new $class($this->db);
        
        return $driver->truncate($this->table);
    }
    
    public function is_unique($column, $value)
    {
        $result = $this->_get($value, $column, DB::ALL);
        
        return ($result) ? FALSE : TRUE;
    }
    
    protected function _get($id = NULL, $column = 'id', $flag=NULL)
    {
        $db = $this->db->select()->from($this->table);
        
        if($id !== NULL) $db->where($column, '=', $id);
        
        return $db->execute($flag);       
    }
}
