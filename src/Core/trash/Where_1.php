<?php
namespace Core\DB\Traits;
/**
 * Description of Where
 *
 * @author JackRabbit
 */
trait Where
{
//    public function where1($column, $compare, $value, $postfix='')
//    {
//        $this->_where($column, $compare, $value, $postfix);        
//        return $this;
//    }
    
    public function where($column, $compare, $value = NULL, $postfix = 'AND', $prefix = '')
    {
        if(!empty($postfix)) $postfix = ' '.$postfix;
        
        if($value === NULL)
        {
            if($compare === '=') $compare = 'IS NULL';
            else $compare = 'IS NOT NULL';
            
            $this->where[] = $prefix.' "'.$column.'" '.$compare.$postfix;
        }
        elseif($value instanceof \stdClass)
        {
            $this->params = array_merge($this->params, $value->params);
            $this->where[] = $prefix.' '.$value->sql.$postfix;           
        }
        elseif(!is_array($value))
        {
//            if($value !== NULL)
                $this->params[] = $value;
            $this->where[] = $prefix.' "'.$column.'" '.$compare.' ?'.$postfix;
        }
        else
        {
            if(count($value) === 0) return $this;
//            if($value !== NULL)
                $this->params = array_merge($this->params, $value);
            $qm = '('.str_repeat('?, ', count($value) - 1).' ?)';
            $this->where[] = $prefix.' "'.$column.'" '.$compare.' '.$qm.$postfix;
        }
        
        return $this;
    }
    
//    public function or_where($column, $compare, $value)
//    {    
//        $this->_where($column, $compare, $value, 'OR');       
//        return $this;
//    }
//    
//    protected function _where1($column, $compare, $value, $cond)
//    {
//        if($value instanceof Expression)
//        {
//            $this->where[] = ' '.$cond.' '.$column.' '.$compare.' '.$value->expr;
//        }
//        elseif($value instanceof \stdClass)
//        {
//            $this->params = array_merge($this->params, $value->params);
//            $this->where[] = ' '.$cond.' '.$value->sql;           
//        }
//        elseif(!is_array($value))
//        {
//            $this->params[] = $value;
//            $this->where[] = ' '.$cond.' '.$column.' '.$compare.' ?';
//        }
//        else
//        {
//            $this->params = array_merge($this->params, $value);
//            $qm = '('.str_repeat('?, ', count($value) - 1).' ?)';
//            $this->where[] = ' '.$cond.' '.$column.' '.$compare.' '.$qm;
//        }
//    }
}
