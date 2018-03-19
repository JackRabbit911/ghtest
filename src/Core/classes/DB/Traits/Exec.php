<?php
namespace Core\DB\Traits;
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
        
        $query = new Query($this->pdo, $obj->sql);
        $query->bind($obj->params);
        return $query->execute($type_result);
    }
    
}
