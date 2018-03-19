<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Core\Route;


//Route::set('test', 'test')->callback(function(){
//    echo '<h1>Hello, World</h1>';
//});

Route::set('test', 'test(/<action>(/<param>))(?<query>)')->defaults(array(
    'controller' => 'test',
    'action' => 'index',
//    'namespace' => 'Docs'
));

Route::set('default1', '(<action>(/<param1>(/<param2>(/<param3>(/<param4>)))))(.<ext>)(?<query>)')
        ->defaults([
//            'namespace'=>'App', 
            'controller'=>'Index', 
            'action'=>'index', 
            'param1'=>'']);

Route::set('default', '(<controller>(/<action>(/<param>))(.<ext>))(?<query>)')
        ->defaults(['controller'=>'index', 'action'=>'index']);

