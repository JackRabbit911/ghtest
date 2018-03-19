<?php

if(\Core\HTTP::domain() == 'webnigger.ru')
{
    return [
        'memcache'  => [
            'server'    => '185.26.122.38',
            'port'      => 11211,
        ],
        'sqlite'    => [
            'driver'    => 'sqlite',
            'path'      => 'App/sqlite',
            'dbname'    => 'db.sdb',
        ],
        'mysql'     => [
            'driver'    => 'mysql',
            'host'      => 'host1365301',
            'dbname'    => 'host1365301_wndb',
            'username'  => 'host1365301_wndb',
            'password'  => 'wndbpass',
        ],
    ];
}
else
{
    return [
        'memcache'  => [
            'server'    => 'localhost',
            'port'      => 11211,
        ],
        'sqlite'    => [
            'driver'    => 'sqlite',
            'path'      => 'App/sqlite',
            'dbname'    => 'db.sdb',
        ],
        'mysql'     => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'dbname'    => 'wndb_db',
            'username'  => 'wndb',
            'password'  => 'wndbpass',
        ],
    ];
}
