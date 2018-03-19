<?php
namespace Core\DB;

/**
 * Description of Insert
 *
 * @author JackRabbit
 */

class Delete
{
    use Traits\Where;
    use Traits\Exec;
    
    protected $pdo;
    
    protected $table;
    protected $params = array();
    protected $where = array();
    
    public function __construct($pdo, $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }
    
    
    
    public function render()
    {       
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
        
        $result->sql = 'DELETE FROM '.$this->table.$where;
        $result->params = $this->params;
        
        return $result;
    }

}
