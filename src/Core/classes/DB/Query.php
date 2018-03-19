<?php
namespace Core\DB;
/**
 * Description of Query
 *
 * @author JackRabbit
 */
use Core\DB AS DB;
use Core\Helper\Arr;
//use Core\Exception;

class Query
{
    public $stmt;
    
    protected $params = array();
    protected $sql_type;
    protected $transaction = FALSE;
    
    public function __construct($pdo, $sql)
    {
        $this->pdo = $pdo;
        $this->stmt = $this->pdo->prepare($sql);
        
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
        if($this->transaction && !$this->pdo->inTransaction()) $this->pdo->beginTransaction();
            
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
//            var_dump($this->params); exit;
            $this->stmt->execute($this->params);
//            var_dump($this->stmt->debugDumpParams()); exit;
        }
        
        if($this->pdo->inTransaction()) $this->pdo->commit();
        
//        $sql_type = substr($this->stmt->queryString,0,strpos(trim($this->stmt->queryString),' '));
        
        if($this->sql_type === 'INSERT') return $this->pdo->lastInsertId();
        elseif($this->sql_type === 'UPDATE' || $this->sql_type === 'DELETE') return $this->stmt->rowCount();
        else
        {
            $this->stmt->setFetchMode(\PDO::FETCH_CLASS, '\Core\DB\Row'); 
            
            if($type_result === DB::ROW) return $this->stmt->fetch();
            elseif($type_result === DB::ALL) return $this->stmt->fetchALL();
            else return $this->stmt;
        }
    }
    
}
