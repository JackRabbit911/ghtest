<?php
//    require dirname(__FILE__).'/adminer-sqlite-login.php';
//    require dirname(__FILE__).'/adminer-4.3.1.php';

    define('DOCROOT', dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR);    
    define('SRCPATH', DOCROOT.'src'.DIRECTORY_SEPARATOR);
    define('ADMPATH', SRCPATH.'App'.DIRECTORY_SEPARATOR.'adminer'.DIRECTORY_SEPARATOR);
    
    define('SQLITE',  SRCPATH.'App'.DIRECTORY_SEPARATOR.'sqlite'.DIRECTORY_SEPARATOR);
//    echo ADMPATH;
//    
//    exit;
    
    require ADMPATH.'adminer-sqlite-login.php';
    require ADMPATH.'adminer-4.3.1.php';