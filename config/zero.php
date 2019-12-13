<?php

use Mathrix\Lumen\Zero\Providers\CacheableServiceProvider;

return [
    'cache'      => CacheableServiceProvider::CACHE_MODE_ALWAYS,
    'namespaces' => [
        'controllers' => 'App\\Controllers',
        'models'      => 'App\\Models',
        'observers'   => 'App\\Observers',
        'policies'    => 'App\\Policies',
        'registrars'  => 'App\\Registrars',
    ],
    'ignore'     => [
        'observers'  => [],
        'registrars' => [],
    ],
];
