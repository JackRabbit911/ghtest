<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

use Core\Controller;
use Core\Route;
use Core\Core;
use Core\Exception\Exception;
use Core\HTTP;
use Core\Cache;
use Core\Helper\File;
use Core\Session;

/**
 * Description of Index
 *
 * @author JackRabbit
 */
class Adm extends Controller\Controller
{
//    public static $regex = '((\w++)(?:::(\$?\w++))?(?:\(\))?)';
    
    public function db()
    {
        $db = new \PDO('sqlite:../src/App/sqlite/a.db');
        $x = $db->exec('CREATE TABLE IF NOT EXISTS tests (id INT(11) NOT NULL PRIMARY KEY AUTOINCREMENT, name VARCHAR(255) NOT NULL)');
        var_dump($x);
    }

}
