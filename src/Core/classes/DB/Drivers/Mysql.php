<?php
namespace Core\DB\Drivers;

/**
 * Description of Sqlite
 *
 * @author JackRabbit
 */
use Core\Helper\Arr;

class Mysql
{
    
    public function __construct($db)
    {
        $this->pdo = $db->pdo;
    }
    
    public function truncate($table)
    {
        $sql = "TRUNCATE TABLE $table";
        
        if(!$this->pdo->inTransaction()) $this->pdo->beginTransaction();     
        $return = $this->pdo->prepare($sql)->execute();
        if($this->pdo->inTransaction()) $this->pdo->commit();
        return $return;
    }
    
    public function tables()
    {
//        $sql = DB::$pdo->getAttribute(\PDO::ATTR_CASE);
        $sql = 'SELECT table_name AS name FROM information_schema.tables WHERE TABLE_TYPE = "BASE TABLE" ORDER BY table_name';
        return $sql;
    }
    
    public function columns($table)
    {
//        $sql = "SELECT * FROM information_schema.columns WHERE table_name = `$table`";
        $sql = "SHOW COLUMNS FROM $table";
        $array = $this->pdo->query($sql)->fetchAll(); //->execute(); //->fetchAll();
        $res = [];
        foreach($array AS $item)
        {
            $res[] = $item->Field;
        }
        return $res;
//        return $array;
    }
    
    public function is_table()
    {
        $sql = 'SELECT table_name FROM information_schema.tables WHERE table_name = ?';
        return $sql;
    }
    
    public function types($type_index)
    {       
        $data_types = [
            'INT'       => ['int', 'integer'],
            'VARCHAR'   => ['varchar', 'string', 'char'],
        ];
        
        $indexes = [
            'NOT NULL PRIMARY KEY AUTO_INCREMENT' => ['primary_ai', 'primary autoincrement', 'primary auto_increment'],
            'NOT NULL PRIMARY KEY UNIQUE' => ['primary_unique', 'primary unique'],
            'NULL' => ['null', 'NULL'],
            'NOT NULL' => ['not_null', 'NOT_NULL']
        ];
        
        
        $arr = explode(' ', $type_index, 2);
        
        
        $type = preg_split('/(\(\d+\))$/', $arr[0], 2, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        
        $type[0] = Arr::get_key($data_types, strtolower($type[0]));
        
        
        if(!empty($arr[1]))
        {
            $index = ' '.Arr::get_key($indexes, strtolower($arr[1]));
        }
        else $index = '';
        
        return ' '. strtoupper(implode('', $type).$index);
    }
    
}
