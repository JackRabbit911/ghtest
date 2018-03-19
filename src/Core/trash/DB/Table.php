<?php
namespace Core\DB;

/**
 * Description of Set
 *
 * @author JackRabbit
 */

use Core\DB;

class Table
{
    public function __construct($table)
    {
        $this->table = $table;  
    }
    
    public function get($id = NULL, $column = 'id')
    {
//        $db = DB::select()->from($this->table);
//        if($id) return $db->where($column, '=', $id)->execute(DB::ROW);
//        else return $db->execute(DB::ALL);
        
        $db = $this->_get($id, $column);
        return $db->execute(DB::ROW);
    }
    
    public function getAll($id = NULL, $column = 'id')
    {
        $db = $this->_get($id, $column);
        return $db->execute(DB::ALL);
    }
   
    public function set(array $data, $id = NULL, $column = 'id')
    {
        if($id === NULL)
            return DB::insert($this->table)->set($data)->execute();
        else
            return DB::update($this->table)->set($data)->where($column, '=', $id)->execute();
    }
    
    public function delete($id, $column = 'id')
    {
        return DB::delete($this->table)->where($column, '=', $id)->execute();
    }
    
    public function truncate()
    {
        $class = __NAMESPACE__.'\Drivers\\'.ucfirst(DB::$driver);
        return $class::truncate($this->table);
    }
    
    protected function _get($id = NULL, $column = 'id')
    {
        $db = DB::select()->from($this->table);
        if($id) $db->where($column, '=', $id);
        return $db;       
    }
}
