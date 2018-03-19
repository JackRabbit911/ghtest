<?php
namespace Core\DB\Drivers;

/**
 * Description of Sqlite
 *
 * @author JackRabbit
 */
//use Core\Model\DB;
use Core\Helper\Arr;

class Sqlite
{
   
    public function __construct($db)
    {
        $this->db = $db;
    }


    public function truncate($table)
    {
//        $table = DB::$pdo->quote($table);
        
        $create = $this->db->select('sql')->from('sqlite_master')
                ->where('type', '=', 'table')
                ->where('tbl_name', '=', $table)
                ->execute(2)
                ->sql;
        
//        return $create;
        
        $drop = "DROP TABLE IF EXISTS $table";
        
        if(!$this->db->pdo->inTransaction()) $this->db->pdo->beginTransaction();
        $this->db->pdo->exec($drop);
        $return = $this->db->pdo->exec($create);
        if($this->db->pdo->inTransaction()) $this->db->pdo->commit();
        return $return;
    }
    
    public function tables()
    {
        $sql = 'SELECT name FROM sqlite_master WHERE type="table" AND tbl_name != "sqlite_sequence" ORDER BY name';
        return $sql;
    }
    
    public function columns($table)
    {
        $sql = "PRAGMA TABLE_INFO ($table)";
        $array = $this->db->pdo->query($sql)->fetchAll(); //\PDO::FETCH_COLUMN);
        $res = [];
        foreach($array AS $item)
        {
            $res[] = $item->name;
        }
        return $res;
//        return $sql;
    }
    
    public function is_table()
    {
        $sql = 'SELECT * FROM sqlite_master WHERE type="table" AND tbl_name = ?';
        return $sql;
    }
    
    public function types($type_index)
    {       
        $data_types = [
            'INTEGER'   => ['int', 'integer'],
            'VARCHAR'   => ['varchar', 'string', 'char'],
            'REAL'      => ['real', 'doble', 'float'],
            'NUMERIC'   => ['numeric', 'date', 'num']
        ];
        
        $indexes = [
            'NOT NULL PRIMARY KEY AUTOINCREMENT' => ['primary_ai', 'primary autoincrement', 'primary auto_increment'],
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
