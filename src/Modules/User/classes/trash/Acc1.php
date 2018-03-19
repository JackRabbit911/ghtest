<?php
namespace User;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Form
 *
 * @author JackRabbit
 */
use User\Model\Roles;
//use Core\Helper\Arr;

trait Acc
{
//    const ROOT = 100;


    protected $roles = [];
    
    protected $model_roles;
    
    public function roles($role = NULL)
    {               
        return ($role) ? in_array($role, $this->roles) : $this->roles;
    }
    
    public function set_role($role)
    {
        $this->roles[] = $role;
    }
    
    public function set_roles()
    {
        if(!$this->model_roles) $this->model_roles = new Roles();
        
        $roles = func_get_args();
        
//        function _add($role)
//        {
//            if(!in_array($role, $this->roles)) $this->roles[] = $role;
//        }
        
        $users_roles = $this->model_roles->get($this->id);
        
//        var_dump($users_roles);
        
//        $users_roles += $roles;
        
        $this->roles = array_unique(array_merge($this->roles, $users_roles, $roles));
        
//        _add($role);
    }
    
    public function role($role)
    {
        if(!$this->model_roles) $this->model_roles = new Roles();
        
        return $this->model_roles->is_rights($this->id, $role);
    }
    
    public function group($role)
    {
        if(!$this->model_roles) $this->model_roles = new Roles();
        
//        $users_roles = $this->model_roles->in_group($this->id, $role);
        
        return $this->model_roles->_minors($role);
    }
    
    public function in_group($role)
    {
        if(!$this->model_roles) $this->model_roles = new Roles();
        
        $roles = $this->model_roles->get_like($this->id, $role);
        
//        var_dump($roles); exit;
        
        return (empty($roles)) ? FALSE : TRUE;
    }
    
    public function acc($role)
    {
        if(!$this->model_roles) $this->model_roles = new Roles();
        
        return $this->model_roles->_roles($role);
    }
}
