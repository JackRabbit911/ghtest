<?php
use Core\Core;
use Core\Route;
use Core\I18n as I18n;

Core::vars('errors', TRUE);
Core::vars('cache.output', FALSE);
Core::vars('cache.compress', 9);
Core::vars('cache.life_time', 60);
Core::vars('index_file', FALSE);
//Core::vars('errors', TRUE);


//Core::$cache = TRUE;

I18n::$base_lang = 'en';
I18n::$current_lang = 'ru';
I18n::detect_lang_method(FALSE);

Route::set('media', 'media(/<file>)')
                ->filter(array('file' => '.+'))
                ->defaults(array(
                    'namespace' => 'Core',
                    'controller' => 'media',
                    'action'     => 'index',
                    'file'       => NULL,
            ));      


