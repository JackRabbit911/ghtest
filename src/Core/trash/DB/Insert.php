<?php
namespace Core\DB;

/**
 * Description of Insert
 *
 * @author JackRabbit
 */
use Core\Helper\Arr;

class Insert
{
    use \Core\DB\QueryBuilder\Exec;
    
    protected $table;
    
    public function __construct($table)
    {
        $this->table = $table;
    }
    
    public function set(array $data)
    {
        if(Arr::is_multidimensional($data) === TRUE)
            $arr_data = reset($data);
        else $arr_data = &$data;
        
        $this->set = implode(', ', array_keys($arr_data));
        $this->values = rtrim(str_repeat('?, ', count($arr_data)), ', ');
        
        array_walk($data, function(&$item, $k){
            if(is_array($item))
            {
                $item = array_values($item);
            }
        });
        
        if(Arr::is_assoc($data)) $this->params = array_values($data);
        
        return $this;
    }
    
    public function render()
    {
        $result = new \stdClass();
        
        $result->sql = 'INSERT INTO '.$this->table.' ('.$this->set.') VALUES ('.$this->values.')';
        $result->params = $this->params;
        
        return $result;
    }

}
