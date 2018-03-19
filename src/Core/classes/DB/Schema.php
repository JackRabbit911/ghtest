<?php
namespace Core\DB;

/**
 * Description of Set
 *
 * @author JackRabbit
 */

//use Core\DB;

class Schema
{
    protected $db;
    protected $driver;
    
    public function __construct($db)
    {
//        $this->pdo = $pdo;
        $this->db = &$db;
        $driver = __NAMESPACE__.'\Drivers\\'.ucfirst($this->db->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
        
        $this->driver = new $driver($this->db);
        
//        $this->driver = 'Core\Model\DB\Drivers\\'.ucfirst($driver);
//        $this->driver = new $driver();
    }
    
    public function tables()
    {
//        $class = $this->driver;
        $sql = $this->driver->tables();
        return $this->db->query($sql)->execute()->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    public function columns($table)
    {
//        $class = $this->driver;
        return $this->driver->columns($table);
//        return DB::query($sql)->execute(1);
    }
    
    public function indexes($table)
    {
        
    }
    
    public function is_table($table)
    {
//        $class = $this->driver;
        $sql = $this->driver->is_table();
        $result = $this->db->query($sql)->bind(array($table))->execute(2);
        
        return ($result) ? TRUE : FALSE;
    }
    
    public function is_column($table, $column)
    {
        $columns = $this->columns($table);
        
        return (in_array($column, $columns)) ? TRUE : FALSE;
    }
    
    public function primary_key($table)
    {
        
    }
}
