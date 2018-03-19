<?php
namespace Core\DB;

/**
 * Description of Insert
 *
 * @author JackRabbit
 */

class Create
{   
    protected $db;
    protected $pdo;
    protected $driver;
    
    protected $table;
    
//    protected $params = [];
    
    protected $str_fields;
    
    protected $columns = [];
    protected $engine = NULL;
    protected $collate = NULL;
    
    public function __construct($db, $table)
    {       
        $this->db = $db;
        $this->pdo = $db->pdo;
        
        $driver = __NAMESPACE__.'\Drivers\\'.ucfirst($db->driver);
        $this->driver = new $driver($db);
        
        $this->table = $table;
        
        if($db->driver === 'mysql')
        {
            $this->engine = ' ENGINE=InnoDB';
            $this->collate = ' COLLATE utf8_general_ci';
        }
    }
    
    public function columns(array $columns)
    {
        $fields = [];
        foreach($columns AS $column=>$type)
        {
            $this->column($column, $type);
        }
        return $this;
    }
    
    public function engine($engine = 'InnoDB')
    {
        if(in_array($this->db->driver, ['mysql']))
        {
            $this->engine = ' ENGINE='.$engine;
        }
        
        return $this;
    }
    
    public function collate($collate = 'utf8_general_ci')
    {
        if(in_array($this->db->driver, ['mysql']))
        {
            $this->collate = ' COLLATE '.$collate;
        }
        return $this;
    }
    
    public function column($column, $type_index)
    {
        $type = $this->driver->types($type_index);
                
        $this->columns[] = $column.' '.$type;
        
        return $this;
    }    

    public function execute()
    {        
        
//        var_dump($columns);
        
        
        $columns = implode(', ', $this->columns);
        $sql = 'CREATE TABLE IF NOT EXISTS '.$this->table.' ('.$columns.')'.$this->engine.$this->collate;
        
        if(!$this->pdo->inTransaction()) $this->pdo->beginTransaction();
        $return = $this->pdo->exec($sql);
        if($this->pdo->inTransaction()) $this->pdo->commit();
        return $return;
    }
}
