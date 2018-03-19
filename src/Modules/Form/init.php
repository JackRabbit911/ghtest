<?php

use Core\Route;

Route::set('form', 'form/<class>/<method>(/<param>)')
        ->defaults([
            'namespace' => 'form',
            'controller' => 'form',
            'action' => 'index',
            ]);
