<?php
namespace Core\DB\Drivers;

/**
 * Description of Sqlite
 *
 * @author JackRabbit
 */
use Core\DB;

class Mysql
{
    
    
    public static function truncate($table)
    {
        $sql = "TRUNCATE TABLE $table";
        
        if(!DB::$pdo->inTransaction()) DB::$pdo->beginTransaction();     
        $return = DB::$pdo->prepare($sql)->execute();
        if(DB::$pdo->inTransaction()) DB::$pdo->commit();
        return $return;
    }
    
    public static function tables()
    {
//        $sql = DB::$pdo->getAttribute(\PDO::ATTR_CASE);
        $sql = 'SELECT table_name AS name FROM information_schema.tables WHERE TABLE_TYPE = "BASE TABLE" ORDER BY table_name';
        return $sql;
    }
    
    public static function columns($table)
    {
//        $sql = "SELECT * FROM information_schema.columns WHERE table_name = `$table`";
        $sql = "SHOW COLUMNS FROM $table";
        $array = DB::$pdo->query($sql)->fetchAll(); //->execute(); //->fetchAll();
        $res = [];
        foreach($array AS $item)
        {
            $res[] = $item->Field;
        }
        return $res;
//        return $array;
    }
    
    public static function is_table()
    {
        $sql = 'SELECT table_name FROM information_schema.tables WHERE table_name = ?';
        return $sql;
    }
    
}
