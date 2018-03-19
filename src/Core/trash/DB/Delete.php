<?php
namespace Core\DB;

/**
 * Description of Insert
 *
 * @author JackRabbit
 */

class Delete
{
    use \Core\DB\QueryBuilder\Where;
    use \Core\DB\QueryBuilder\Exec;
    
    protected $table;
    protected $params = array();
    protected $where = array();
    
    public function __construct($table)
    {
        $this->table = $table;
    }
    
    
    
    public function render()
    {       
        $where = '';       
        foreach($this->where AS $key=>$value)
        {
            $k = key($value);
            
            if($key === 0) $prefix = ' WHERE ';
            else $prefix = ' '.$k.' ';
              
            $where .= $prefix.$value[$k];
        }
        
        
        $result = new \stdClass();
        
        $result->sql = 'DELETE FROM '.$this->table.$where;
        $result->params = $this->params;
        
        return $result;
    }

}
