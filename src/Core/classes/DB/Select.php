<?php
namespace Core\DB;

/**
 * Description of Select
 *
 * @author JackRabbit
 */

//use Core\Exception\Exception;
//use Core\DB\Expression;
use Core\Helper\Arr;

class Select
{
    use Traits\Where;
    use Traits\Exec;
    
    protected $pdo;

    protected $sql = 'SELECT ';
    protected $params = array();
//    protected $where = array();
//    protected $or_where = array();
    protected $from = '';
    protected $join = array();
    protected $columns = array();
    protected $order = array();
    
    public function __construct($pdo, array $fields)
    {
//        parent::__construct($pdo);
        $this->pdo = $pdo;
        $this->columns = $fields;
    }
    
    public function from()
    {         
        $this->from = func_get_args();   
        return $this;
    }
    
    public function join($table, $column, $parent_column, $join='default')
    {
        $joins = array(
            'default'   => '',
            'left'      => 'LEFT ',
            'right'     => 'RIGHT ',
            'inner'     => 'INNER ',
            'outer'     => 'OUTER ',
        );
        
        $prefix = Arr::get($joins, strtolower($join), '').' JOIN ';
        $this->join[] = (object)[
            'prefix'    => $prefix,
            'table'     => $table,
            'column'    => $column,
            'parent_column' => $parent_column];
        
        return $this;
    }
    
    public function order()
    {       
        if(func_num_args() === 2 && is_string(func_get_arg(0)) && is_string(func_get_arg(1)) && in_array(strtoupper(func_get_arg(1)), array('ASC', 'DESC')))
        {
            $this->order[] = func_get_arg(0).' '.func_get_arg(1);
        }
        else
        {
            foreach(func_get_args() AS $order)
            {
                if(is_string($order)) $order_item[] = $order;
                elseif(Arr::is_assoc($order) && count($order) === 1)
                {
                    $order_item[] = key($order).' '.current($order);
                }
                elseif(is_array($order) && count($order) === 2)
                {
                    $order_item[] = $order[0].' '.$order[1];
                }
            }

            $this->order[] = implode(', ', $order_item);
        }
        
        return $this;
    }
    
    public function expr($expr)
    {
        $this->where[] = $expr;
        return $this;
    }
    
    public function render()
    {
        if(empty($this->columns)) $this->columns = ['*'];
        $select = 'SELECT '.implode(', ', $this->columns);
        
        
        $from = ' FROM ';
        $params = array();
        foreach($this->from AS $table)
        {
            if($table instanceof \stdClass)
            {
                $params = array_merge($params, $table->params);
                $table = '('.$table->sql.')';
            }
            
            $from .= $table.', ';
            
        }
        
        $this->params = array_merge($params, $this->params);
        $from = rtrim($from, ', ');
        
        
        $join = '';
        foreach($this->join AS $item_join)
        {
            if($item_join->table instanceof \stdClass)
            {
                $this->params = array_merge($this->params, $item_join->table->params);
                $item_join->table = '('.$item_join->table->sql.')';
            }
            
            $join .= ' '.$item_join->prefix.$item_join->table.' ON '.$item_join->column.' = '.$item_join->parent_column;
        }
        
        if(!empty($this->where)) $this->where = ' WHERE'.$this->where;
               
        if(!empty($this->order))
            $order = ' ORDER BY '.implode(', ', $this->order);
        else $order = '';
        
        $result = new \stdClass();        
        $result->sql = $select.$from.$join.$this->where.$order;
        $result->params = $this->params;
        
        return $result;
    }
}
