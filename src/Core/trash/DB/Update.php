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
    use \Core\DB\QueryBuilder\Where;
    use \Core\DB\QueryBuilder\Exec;
    
    protected $table;
    protected $set = '';
    protected $params = array();
    protected $where = array();
    
    public function __construct($table)
    {
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
        
        $where = '';       
        foreach($this->where AS $key=>$value)
        {
            $k = key($value);
            
            if($key === 0) $prefix = ' WHERE ';
            else $prefix = ' '.$k.' ';
              
            $where .= $prefix.$value[$k];
        }
        
        
        $result = new \stdClass();
        
        $result->sql = 'UPDATE '.$table.' SET '.$this->set.$where;
        $result->params = $this->params;
        
        return $result;
    }

}
