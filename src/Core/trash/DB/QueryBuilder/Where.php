<?php
namespace Core\DB\QueryBuilder;
/**
 * Description of Where
 *
 * @author JackRabbit
 */
trait Where
{
    public function where($column, $compare, $value)
    {
        $this->_where($column, $compare, $value, 'AND');        
        return $this;
    }
    
    public function or_where($column, $compare, $value)
    {    
        $this->_where($column, $compare, $value, 'OR');       
        return $this;
    }
    
    protected function _where($column, $compare, $value, $cond)
    {
        if($value instanceof Expression)
        {
            $this->where[][$cond] = $column.' '.$compare.' '.$value->expr;
        }
        elseif($value instanceof \stdClass)
        {
            $this->params = array_merge($this->params, $value->params);
            $this->where[][$cond] = $value->sql;           
        }
        elseif(!is_array($value))
        {
            $this->params[] = $value;
            $this->where[][$cond] = $column.' '.$compare.' ?';
        }
        else
        {
            $this->params = array_merge($this->params, $value);
            $qm = '('.str_repeat('?, ', count($value) - 1).' ?)';
            $this->where[][$cond] = $column.' '.$compare.' '.$qm;
        }
    }
}
