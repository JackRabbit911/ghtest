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
    const ROW = 2;
    const ALL = 1;
    
    public static $connect = 'sqlite';
    
    protected static $instance = [];
    
    public $driver;
    
    public static function instance($connect = NULL)
    {
        if($connect === NULL) $connect = static::$connect;
        
//        var_dump($connect); exit;
        
        if(is_string($connect))
        {
            $key = $connect;
            $connect = Core::config('connect', $key);
        }
        elseif(is_array($connect))
        {
            $key = md5(serialize($connect));
        }
        
//        var_dump($connect); //exit;
        
        $driver = strtolower($connect['driver']);
        
        $func = 'connect_'.$driver;
        
//        echo $func; exit;
        
        $pdo = call_user_func('static::'.$func, $connect);
        
        if(!isset(self::$instance[$key]) || !(self::$instance[$key] instanceof self))
        {
            self::$instance[$key] = new self($pdo);
        }
        return self::$instance[$key];
    }
    
    protected static function connect_sqlite(array $dsn)
    {
        $dsn['path'] = str_replace('/', DIRECTORY_SEPARATOR, $dsn['path']);
        
        if(strpos($dsn['path'], SRCPATH) === FALSE) $dsn['path'] = SRCPATH.$dsn['path'];
        
        $connect = 'sqlite:'.$dsn['path'].DIRECTORY_SEPARATOR.$dsn['dbname'];
        
        return new \PDO($connect);
    }
    
    protected static function connect_mysql(array $dsn)
    {
        $connect = 'mysql:dbname='.$dsn['dbname'].';host='.$dsn['host'];
        
        return new\PDO($connect, $dsn['username'], $dsn['password']);
    }
    
    protected function __construct(\PDO $pdo)
    {
        $this->pdo = &$pdo;
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        
//        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Foo'); 
        
        $this->driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }
    
    

    public function expr($str)
    {
        return new Expression($str);
    }
    
    public function query($sql)
    {
//        if(!static::$pdo) static::connect();
        return new Query($this->pdo, $sql);
       
    }
    
    public function beguin()
    {
        $this->pdo->beginTransaction();
        return $this;
    }
    
    public function end()
    {
        $this->pdo->commit();
        return $this;
    }
    
    public function select()
    {               
        $args = func_get_args();
        
        $columns = [];
        foreach($args AS $item)
        {
            $columns[] = '"'.trim($item, '"').'"';
        }
        
        return new Select($this->pdo, $columns);
    }
    
    public function insert($table)
    {
//        if(!static::$pdo) static::connect();
        return new Insert($this->pdo, $table);
    }
    
    public function update($table)
    {
//        if(!static::$pdo) static::connect();
        return new Update($this->pdo, $table);
    }
    
    public function delete($table)
    {
//        if(!static::$pdo) static::connect();
        return new Delete($this->pdo, $table);
    }
    
    public function drop($table)
    {
        $sql = 'DROP TABLE '.$table;
        if(!$this->pdo->inTransaction()) $this->pdo->beginTransaction();
        $return = $this->pdo->exec($sql);
        if($this->pdo->inTransaction()) $this->pdo->commit();
        return $return;
    }
    
    public function create($table)
    {
//        if(!static::$pdo) static::connect();
        return new Create($this, $table);
    }
    
    public function truncate($table)
    {
        $class = __NAMESPACE__.'\Drivers\\'.ucfirst($this->driver);
        
        $driver = new $class($this->db);
        
        return $driver->truncate($table);
    }

    public function table($table)
    {
//        if(!static::$pdo) static::connect();
        return new Table($this, $table);
    }
    
    public function schema()
    {
//        if(!static::$pdo) static::connect();
        return new Schema($this);
    }
    
}
