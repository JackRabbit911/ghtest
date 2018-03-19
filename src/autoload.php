<?php

require_once SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Autoload.php';

$autoloader = Core\Autoload::instance();

// the first load module "App" (application)
$autoloader->addModule('App');

// here load all necessary modules that we use
$autoloader->addModule(['Modules/Form' => 'Form', 'Modules/User' => 'User']);
//$autoloader->addModule('Modules/User');

// then load modules for the develop and testing mode. And set error_reporting.
if($autoloader::$enviroment >= TESTING)
{
    $autoloader->addModule('Dev/Docs', 'Docs');
//    $autoloader->addModule('Dev/UnitTests', 'UnitTests');
    error_reporting(-1);
}
else error_reporting(0);

// In the end, connect the module Core
$autoloader->addModule('Core')->register();
    

