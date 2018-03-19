<?php
namespace Core\DB;

/**
 * Description of Set
 *
 * @author JackRabbit
 */

use Core\DB;

class Schema
{
    protected $driver;
    
    public function __construct($driver)
    {
        $this->driver = 'Core\DB\Drivers\\'.ucfirst($driver);
//        $this->driver = new $driver();
    }
    
    public function tables()
    {
        $class = $this->driver;
        $sql = $class::tables();
        return DB::query($sql)->execute()->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    public function columns($table)
    {
        $class = $this->driver;
        return $class::columns($table);
//        return DB::query($sql)->execute(1);
    }
    
    public function indexes($table)
    {
        
    }
    
    public function is_table($table)
    {
        $class = $this->driver;
        $sql = $class::is_table();
        $result = DB::query($sql)->bind(array($table))->execute(2);
        
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
