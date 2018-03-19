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
//use User\Model\Token\TokenInterface;
use Core\Core;

class Mysql extends Sql
{
    public static $connect = [];
    
    public function __construct()
    {
        static::$connect = Core::config('connect', 'mysql');
        
        parent::__construct();
    }
}
