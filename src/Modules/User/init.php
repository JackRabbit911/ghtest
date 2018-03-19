<?php

use Core\Route;

Route::set('user', 'user/<action>(/<param>)')
        ->defaults([
            'namespace'=>'user', 
            'controller'=>'index', 
            ]);
