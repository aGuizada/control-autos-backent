<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sanctum Statefulness
    |--------------------------------------------------------------------------
    |
    | This option controls which domains can receive stateful authentication
    | cookies. You may specify a comma separated list of domains, or use a
    | wildcard ("*") to allow all domains to receive stateful cookies.
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', '')),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guard
    |--------------------------------------------------------------------------
    |
    | Here you may specify the guard that Sanctum will use when authenticating
    | users. This will usually be "web", but if you are using a custom guard
    | you can specify it here.
    |
    */

    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Sanctum Prefix
    |--------------------------------------------------------------------------
    |
    | This value will be used to prefix all Sanctum routes. You can change this
    | if you wish to have your Sanctum routes under a different URI.
    |
    */

    'prefix' => 'api',
];
