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
use Core\Pattern\DataIterator;
//use Core\Session;
//use User\Model\Auth as ModelAuth;


//use \User\Group;

abstract class UserAbstract extends DataIterator //implements Accble
{
//    protected $data = [];
    
    public function save()
    {
//        var_dump($this->data);
        return Model\Auth::set($this->data);
    }
}
