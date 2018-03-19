<?php
namespace Core\Session;

/**
 * Description of Files
 *
 * @author JackRabbit
 */
use Core\Session;

class Files
{
    protected $file;
    protected $session_id;
    
    public function __construct()
    {
//        if(!is_dir(Session::$save_path))
//        {
//            mkdir(Session::$save_path, 0755);
//        }
//
//        if(!is_writable(Session::$save_path))
//        {
//            chmod(Session::$save_path, 0755);
//        }    
        
//        var_dump(session_id()); exit;
        
        Session::mkdir();
        
        session_save_path(Session::$save_path);
        $this->file = Session::$save_path.'sess_'.session_id();
        if(file_exists($this->file))
        {
            touch($this->file);
            echo $this->file;
        }
    }
    
    public function last_activity()
    {
//        $file = static::$save_path.static::$prefix.session_id();
        if(file_exists($this->file)) return filemtime($this->file);
        else return FALSE;
    }
    
    public function regenerate($strict = 1)
    {
        if($strict === 0) return;
        elseif($strict === 1)
        {
//            echo '<br>';
            
            
            $old_sid = Session::get('old_sid');
            $file = Session::$save_path.'sess_'.$old_sid;
            
//            var_dump(file_exists($file));
            
            if(file_exists($file)) @unlink($file);
            
            
//            echo ' '.$file.' ';
//            var_dump(file_exists($file));
//            echo '<br>';
            
            $_SESSION['old_sid'] = session_id();
            session_regenerate_id();
        }
        else session_regenerate_id(TRUE);
    }
    
    public function destroy($sid)
    {
//        $old_sid = Session::get('old_sid');
        $file = Session::$save_path.DIRECTORY_SEPARATOR.'sess_'.$sid;
        if(file_exists($file)) @unlink($file);
        return TRUE;
    }
        
    public function gc($lifetime)
    {
        $count = 0;
        if (file_exists(Session::$save_path))
        {    
            $now = time();
            foreach (glob(Session::$save_path.'sess_*') as $file)
            {               
                $endtime = $lifetime + filemtime($file);
                
                
                
                if($endtime < $now && !is_dir($file))
                {
                    @unlink($file);
//                    echo $endtime-$now.'<br>';
                    $count++;
                }
//                else return 'huy';
            }
//            return glob(Session::$save_path);
        }
//        else return Session::$save_path;
        
        
        return $count;
    }
    
    
}
