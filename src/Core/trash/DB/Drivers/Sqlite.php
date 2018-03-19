<?php
namespace Core\DB\Drivers;

/**
 * Description of Sqlite
 *
 * @author JackRabbit
 */
use Core\DB;

class Sqlite
{
    
    
    public static function truncate($table)
    {
//        $table = DB::$pdo->quote($table);
        
        $create = DB::select('sql')->from('sqlite_master')
                ->where('type', '=', 'table')
                ->where('tbl_name', '=', $table)
                ->execute(2)
                ->sql;
        
//        return $create;
        
        $drop = "DROP TABLE IF EXISTS $table";
        
        if(!DB::$pdo->inTransaction()) DB::$pdo->beginTransaction();
        DB::$pdo->exec($drop);
        $return = DB::$pdo->exec($create);
        if(DB::$pdo->inTransaction()) DB::$pdo->commit();
        return $return;
    }
    
    public static function tables()
    {
        $sql = 'SELECT name FROM sqlite_master WHERE type="table" AND tbl_name != "sqlite_sequence" ORDER BY name';
        return $sql;
    }
    
    public static function columns($table)
    {
        $sql = "PRAGMA TABLE_INFO ($table)";
        $array = DB::$pdo->query($sql)->fetchAll(); //\PDO::FETCH_COLUMN);
        $res = [];
        foreach($array AS $item)
        {
            $res[] = $item->name;
        }
        return $res;
//        return $sql;
    }
    
    public static function is_table()
    {
        $sql = 'SELECT * FROM sqlite_master WHERE type="table" AND tbl_name = ?';
        return $sql;
    }
    
}
