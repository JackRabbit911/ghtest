<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace User\Model\Token;

/**
 * Description of Sqlite
 *
 * @author JackRabbit
 */
use User\Model\Token\TokenInterface;
//use Core\DB;

class Files implements TokenInterface
{   
    public static $save_path = SRCPATH.'App'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.'tokens'.DIRECTORY_SEPARATOR;
    
    protected $file;

    public function __construct()
    {
        static::_mkdir();
//        if(!$this->file)
//            $this->file = static::$save_path.DIRECTORY_SEPARATOR.$token;
//        $this->file = static::$save_path.DIRECTORY_SEPARATOR.$token;
    }
    
    public function get($token)
    {
//        $file = static::$save_path.DIRECTORY_SEPARATOR.$token;
        
        $this->_file($token);
        
        if(file_exists($this->file))
        {
            $result = unserialize(file_get_contents($this->file));
            $row = new \Core\DB\Row;
            $row->set_array($result);
            return $row;
        }
        else return NULL;
    }
    
    public function set($data, $token)
    {
        $this->_file($token);
        
        $old_file = $this->file;
        
        $new_file = static::$save_path.DIRECTORY_SEPARATOR.$data['token'];
        
        $this->file = $new_file;
        unset($data['token']);
        
        file_put_contents($new_file, serialize($data));
        
//        $old_file = static::$save_path.DIRECTORY_SEPARATOR.$token;
        
        if(file_exists($old_file)) @unlink ($old_file);
    }
    
    public function delete($token)
    {
//        $file = static::$save_path.DIRECTORY_SEPARATOR.$token;
        $this->_file($token);
        
        if(file_exists($this->file))
        {
            @unlink($this->file);
            $this->file = NULL;
        }
    }
    
    public function is_unique($token)
    {  
        
        $file = static::$save_path.DIRECTORY_SEPARATOR.$token;
//        $this->_file($token);
        
//        var_dump(file_exists($file));
        
//        return TRUE;
        
        return (!file_exists($file)) ? TRUE : FALSE;
    }
    
    public function gc($lifetime)
    {
        $count = 0;
        if (file_exists(static::$save_path))
        {    
            $now = time();
            foreach (glob(static::$save_path.'*') as $file)
            {               
                $endtime = $lifetime + filemtime($file);
                                
                if($endtime < $now && !is_dir($file))
                {
                    @unlink($file);
                    $count++;
                }
            }
        }        
        return $count;
    }
    
    protected function _file($token)
    {
        if(!$this->file)
            $this->file = static::$save_path.DIRECTORY_SEPARATOR.$token;
    }
    
    protected static function _mkdir()
    {
        if(!is_dir(static::$save_path))
        {
            mkdir(static::$save_path, 0755);
        }

        if(!is_writable(static::$save_path))
        {
            chmod(static::$save_path, 0755);
        }                   
    }
}
