<?php
namespace Core;

/**
 * Description of DB
 *
 * @author JackRabbit
 */
use Core\Core;
use Core\DB\Query;
use Core\DB\Select;
use Core\DB\Insert;
use Core\DB\Update;
use Core\DB\Delete;
use Core\DB\Create;
use Core\DB\Table;
use Core\DB\Expression;
use Core\DB\Schema;

class DB
{
    use Singletone;
    
    
    const ROW = 2;
    const ALL = 1;

    public static $driver = 'sqlite';
    protected static $sqlite_path = SRCPATH.'App'.DIRECTORY_SEPARATOR.'sqlite'.DIRECTORY_SEPARATOR;
    protected static $dbname = 'db.sdb';
//    protected static $connection = array();
    public static $pdo;
//    protected static $stmt = array();
    

    
    public static function connect($driver = NULL, array $dsn = NULL)
    {
        if($driver === NULL) $driver = static::$driver;
        if($dsn === NULL) $dsn = Core::config('connect', $driver);
        
        $func = 'connect_'.$driver;                   
        static::$pdo = call_user_func(array('\Core\DB', $func), $dsn);
        
        static::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        static::$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        
        return static::instance();
    }
    
    public static function disconnect()
    {
        static::$pdo = NULL;
    }


    protected static function connect_sqlite(array $dsn)
    {
        if(!isset($dsn['path'])) $dsn['path'] = static::$sqlite_path;
        if(!isset($dsn['dbname'])) $dsn['dbname'] = static::$dbname;
        
        $dsn['path'] = str_replace('/', DIRECTORY_SEPARATOR, $dsn['path']);
        
        if(strpos($dsn['path'], SRCPATH) === FALSE) $dsn['path'] = SRCPATH.$dsn['path'];
        
        $connect = 'sqlite:'.$dsn['path'].DIRECTORY_SEPARATOR.$dsn['dbname'];
        
//        return $connect;
      
        return new \PDO($connect);
    }
    
    protected static function connect_mysql(array $dsn)
    {
        $connect = 'mysql:dbname='.$dsn['dbname'].';host='.$dsn['host'];
        
        return new\PDO($connect, $dsn['username'], $dsn['password']);
    }

    public static function expr($str)
    {
        return new Expression($str);
    }
    
    public static function query($sql)
    {
        if(!static::$pdo) static::connect();
        return new Query($sql);
       
    }
    
    public function beguin()
    {
        static::$pdo->beginTransaction();
        return $this;
    }
    
    public function end()
    {
        static::$pdo->commit();
        return $this;
    }
    
    public static function select()
    {
        if(!static::$pdo) static::connect();
        return new Select(func_get_args());
    }
    
    public static function insert($table)
    {
        if(!static::$pdo) static::connect();
        return new Insert($table);
    }
    
    public static function update($table)
    {
        if(!static::$pdo) static::connect();
        return new Update($table);
    }
    
    public static function delete($table)
    {
        if(!static::$pdo) static::connect();
        return new Delete($table);
    }
    
    public static function drop($table)
    {
        $sql = 'DROP TABLE '.$table;
        if(!DB::$pdo->inTransaction()) DB::$pdo->beginTransaction();
        $return = DB::$pdo->exec($sql);
        if(DB::$pdo->inTransaction()) DB::$pdo->commit();
        return $return;
    }
    
    public static function create($table)
    {
        if(!static::$pdo) static::connect();
        return new Create($table);
    }
    
    public static function truncate($table)
    {
        $class = __NAMESPACE__.'\Drivers\\'.ucfirst(DB::$driver);
        return $class::truncate($table);
    }

    public static function table($table)
    {
        if(!static::$pdo) static::connect();
        return new Table($table);
    }
    
    public static function schema()
    {
        if(!static::$pdo) static::connect();
        return new Schema(static::$driver);
    }
    
//    protected static function _sqlite(array $array = array())
//    {
//        $paramsPDO = new \stdClass();
//        
//        if(isset($array['path'])) $path = SRCPATH.$array['path'].DIRECTORY_SEPARATOR;
//        else $path = static::$sqlite_path; //SRCPATH.'App'.DIRECTORY_SEPARATOR.'sqlite'.DIRECTORY_SEPARATOR;
//        
//        if(!isset($array['dbname'])) $array['dbname'] = 'db.sdb';
//        
//        
//        $paramsPDO->dsn = 'sqlite:'.$path.$array['dbname'];
//        $paramsPDO->username = NULL;
//        $paramsPDO->password = NULL;
//        $paramsPDO->path = $path;
//        
////        static::$dbname = $array['dbname'];
//        
//        return $paramsPDO;
//    }
//    
//    protected static function _mysql(array $array = array())
//    {
//        $paramsPDO = new \stdClass();
//        
//        $paramsPDO->dsn = 'mysql:dbname='.$array['dbname'].';host='.$array['host'];
//        $paramsPDO->username = $array['username'];
//        $paramsPDO->password = $array['password'];
//        
////        static::$dbname = $array['dbname'];
//        
//        return $paramsPDO;
//    }
}
