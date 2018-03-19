<?php
namespace Core\DB\QueryBuilder;
/**
 * Description of Where
 *
 * @author JackRabbit
 */
use Core\DB\Query;

trait Exec
{
    public function execute($type_result = NULL)
    {
        $obj = $this->render();
        
        $query = new Query($obj->sql);
        $query->bind($obj->params);
        return $query->execute($type_result);
    }
    
}
