<?php
namespace Core\DB\Traits;
/**
 * Description of Where
 *
 * @author JackRabbit
 */
trait Where
{
    protected $where;
    
    public function where($column, $compare, $value = NULL)
    {
        $where = $this->_where($column, $compare, $value);
        $this->where .= $where;
        return $this;
    }
    
    public function and_where($column, $compare, $value = NULL)
    {
        $where = $this->_where($column, $compare, $value);
        $this->where .= ' AND '.$where;
        return $this;
    }
    
    public function or_where($column, $compare, $value = NULL)
    {
        $where = $this->_where($column, $compare, $value);
        $this->where .= ' OR '.$where;
        return $this;
    }
    
    public function where_open()
    {
        $this->where .= ' (';
        return $this;
    }
    
    public function where_close()
    {
        $this->where .= ' )';
        return $this;
    }
    
    protected function _where($column, $compare, $value = NULL)
    {       
        $where = '';
        
        if($value === NULL)
        {
            if($compare === '=') $compare = 'IS NULL';
            else $compare = 'IS NOT NULL';
            
            $where .= ' "'.$column.'" '.$compare;
        }
        elseif($value instanceof \stdClass)
        {
            $this->params = array_merge($this->params, $value->params);
            $where .= ' '.$value->sql;           
        }
        elseif(!is_array($value))
        {
                $this->params[] = $value;
            $where .= ' "'.$column.'" '.$compare.' ?';
        }
        else
        {
            if(count($value) === 0) return $this;
                $this->params = array_merge($this->params, $value);
            $qm = '('.str_repeat('?, ', count($value) - 1).' ?)';
            $where .= ' "'.$column.'" '.$compare.' '.$qm;
        }
        
        return $where;
    }
}
