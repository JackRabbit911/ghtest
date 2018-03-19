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
//use Core\Pattern\DataIterator;
//use Core\Session;
//use User\Model\Auth as ModelAuth;


//use \User\Group;

class User extends UserAbstract
{
//    protected $data = [];
    
    public static function factory($id = FALSE)
    {
        return new static($id);
    }


    public function __construct($id)
    {
        if($id) $this->data = Model\User::get($id);
        if($this->data === FALSE) $this->data = [];
    }
    
    public static function getAll()
    {
        return Model\User::getAll();
    }
    
//    public function save()
//    {
//        Model\Auth::set($this->data);
//    }
}
