<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => '',
            'host' => '',
            'name' => '',
            'user' => '',
            'pass' => '',
            'port' => '',
            'charset' => '',
        ],
        'development' => [
            'url' =>'http://weblibrary.loc',
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'weblibrary',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => '',
            'host' => '',
            'name' => '',
            'user' => '',
            'pass' => '',
            'port' => '',
            'charset' => '',
        ]
    ],
    'version_order' => 'creation'
];
