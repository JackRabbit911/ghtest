<?php

if(\Core\HTTP::domain() == 'wn.test')
{
    return [    
            'server'    => 'localhost',
            'port'      => 11211,
        ];
}

if(\Core\HTTP::domain() == 'webnigger.ru')
{
    return [    
            'server'    => '185.26.122.38',
            'port'      => 11211,
        ];
}

