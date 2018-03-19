<?php

use Core\Route;
use Core\Helper\Arr;
//include SRCPATH.'/Core/vendor/Parsedown/Parsedown.php';



Route::set('docs/api', 'docs/api(/<module1>(/<class>(/<method>)))(.<ext>)(?<query>)')
//        ->filter(['module'=>'docs'])
        ->defaults(['namespace'=>'docs', 'controller'=>'api', 'action'=>'index'])
//        ->_namespace('\Docs')
        ;

Route::set('docs', '<module>(/<action>)(.<ext>)(?<query>)')
        ->filter(['module'=>'docs'])
        ->defaults([
            'namespace'=>'docs', 
            'controller'=>'index', 
            'action'=>'index'
            ]);
