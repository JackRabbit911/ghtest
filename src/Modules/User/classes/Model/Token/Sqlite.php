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
//use Core\DB;

class Sqlite extends Sql
{
    public static $connect = [
        'driver'    => 'sqlite',
        'path'      => 'App/sessions',
        'dbname'    => 'tokens.sdb',
    ];
}
