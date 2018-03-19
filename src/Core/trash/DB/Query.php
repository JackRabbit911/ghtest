<?php
namespace Core\DB;
/**
 * Description of Query
 *
 * @author JackRabbit
 */
use Core\DB;
use Core\Helper\Arr;
//use Core\Exception;

class Query
{
    public $stmt;
    
    protected $params = array();
    protected $sql_type;
    protected $transaction = FALSE;
    
    public function __construct($sql)
    {
        $this->stmt = DB::$pdo->prepare($sql);
        
        $this->sql_type = substr($this->stmt->queryString,0,strpos(trim($this->stmt->queryString),' '));
        
        if(strtoupper($this->sql_type) !== 'SELECT') $this->transaction = TRUE;
    }
    
    public function bind(array $params)
    {
        $this->params = array_merge_recursive($this->params, $params);
        return $this;
    }
    
    public function execute($type_result=NULL)
    {
        if($this->transaction && !DB::$pdo->inTransaction()) DB::$pdo->beginTransaction();
            
        if(empty($this->params)) $this->stmt->execute();        
        elseif(Arr::is_multidimensional($this->params) === TRUE)
        {
            $i = 0;
            foreach($this->params AS $params)
            {               
                $this->stmt->execute($params);
                $i++;
            }
        }
        else
        {
            $this->stmt->execute($this->params);
        }
        
        if(DB::$pdo->inTransaction()) DB::$pdo->commit();
        
//        $sql_type = substr($this->stmt->queryString,0,strpos(trim($this->stmt->queryString),' '));
        
        if($this->sql_type === 'INSERT') return DB::$pdo->lastInsertId();
        elseif($this->sql_type === 'UPDATE' || $this->sql_type === 'DELETE') return $this->stmt->rowCount();
        else
        {
            if($type_result === DB::ROW) return $this->stmt->fetch();
            elseif($type_result === DB::ALL) return $this->stmt->fetchALL();
            else return $this->stmt;
        }
    }
    
}
