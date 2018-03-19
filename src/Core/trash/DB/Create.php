<?php
namespace Core\DB;

/**
 * Description of Insert
 *
 * @author JackRabbit
 */
use Core\DB;

class Create
{
//    use \Core\DB\QueryBuilder\Where;
//    use \Core\DB\QueryBuilder\Exec;
    
    protected $table;
    
    public function __construct($table)
    {
        $this->table = $table;
    }
    
    public function columns(array $columns)
    {
        $fields = [];
        foreach($columns AS $column=>$type)
        {
            $fields[] = $column.' '.$type;
        }
        
        $str_fields = ' ('.implode(', ',$fields).')';
        
        $sql = 'CREATE TABLE IF NOT EXISTS '.$this->table.$str_fields;
        
        if(!DB::$pdo->inTransaction()) DB::$pdo->beginTransaction();
        $return = DB::$pdo->exec($sql);
        if(DB::$pdo->inTransaction()) DB::$pdo->commit();
        return $return;
    }

}
