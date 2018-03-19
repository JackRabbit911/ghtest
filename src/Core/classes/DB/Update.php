<?php
namespace Core\DB;

/**
 * Description of Insert
 *
 * @author JackRabbit
 */
//use Core\Helper\Arr;

class Update
{
    use Traits\Where;
    use Traits\Exec;
    
    protected $table;
    protected $set = '';
    protected $params = array();
    protected $where = array();
    
    public function __construct($pdo, $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }
    
    public function set(array $data)
    {
        foreach($data AS $field=>$value)
        {
            $this->set .= $field.' = ?, ';
            $this->params[] = $value;
        }
        $this->set = rtrim($this->set, ', ');
        
        return $this;
    }
    
    public function render()
    {
        if($this->table instanceof \stdClass)
        {
            $this->params = array_merge($this->table->params, $this->params);
            $table = '('.$this->table->sql.')';
        }
        else $table = $this->table;
        
//        $where = '';       
//        foreach($this->where AS $key=>$value)
//        {
//            $k = key($value);
//            
//            if($key === 0) $prefix = ' WHERE ';
//            else $prefix = ' '.$k.' ';
//              
//            $where .= $prefix.$value[$k];
//        }
        $where = ' WHERE';       
        foreach($this->where AS $key=>$value)
        {            
            $where .= $value; //$prefix.$value[$k];
        }
        
        $where = preg_replace('/(AND)$/', '', $where, 1);
        
        
        $result = new \stdClass();
        
        $result->sql = 'UPDATE '.$table.' SET '.$this->set.$where;
        $result->params = $this->params;
        
        return $result;
    }

}
