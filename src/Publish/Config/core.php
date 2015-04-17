<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Frontend theme
    |--------------------------------------------------------------------------
    |
    |
    | Set frontend theme which will be used in \Veemo\Core\Support\FrontendController
    |
    | Default: default
    |
    */

    'frontendTheme' => 'default',


    /*
    |--------------------------------------------------------------------------
    | Backend theme
    |--------------------------------------------------------------------------
    |
    |
    | Set backend theme which will be used in \Veemo\Core\Support\BackendController
    |
    | Default: default
    |
    */

    'backendTheme' => 'default',


    /*
    |--------------------------------------------------------------------------
    | Backend prefix
    |--------------------------------------------------------------------------
    |
    |
    | The prefix that will be used for the administration. Usually admin or backend
    |
    | Default: backend
    |
    */

    'backendPrefix' => 'backend',
    
    
    /*
    |--------------------------------------------------------------------------
    | Backend route settings
    |--------------------------------------------------------------------------
    |
    |
    | The prefix that will be used for the administration. Usually admin or backend
    |
    |
    |
    */

    'backendRouteSettings' => [
        'prefix'        => 'backend',
        'domain'        => '',
        'namespace'     => 'Backend',
        'middleware'    => 'auth.backend'
    ]


);