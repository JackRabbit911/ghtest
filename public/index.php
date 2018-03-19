<?php

/**
 * define paths to core of the site, source folder and system folder
 */
define('DOCROOT', dirname(__DIR__).DIRECTORY_SEPARATOR);
define('SRCPATH', DOCROOT.'src'.DIRECTORY_SEPARATOR);
define('SYSPATH', SRCPATH.'Core'.DIRECTORY_SEPARATOR);

/**
 * define values of the enviroment variable
 */
define('PRODUCTION', 10);
define('STAGING', 20);
define('TESTING', 30);
define('DEVELOPMENT', 40);

require_once SRCPATH.'autoload.php';

Core\Core::instance()->execute();





