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
    | Frontend force ssl
    |--------------------------------------------------------------------------
    |
    |
    | Do we force frontend on https protocol?
    |
    | Default: false
    |
    */

    'frontendForceSsl' => env('FRONTEND_FORCE_SSL', false),


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
    | Backend force ssl
    |--------------------------------------------------------------------------
    |
    |
    | Do we force backend on https protocol?
    |
    | Default: false
    |
    */

    'backendForceSsl' => env('BACKEND_FORCE_SSL', false),


    /*
    |--------------------------------------------------------------------------
    | Allow backend access before auth
    |--------------------------------------------------------------------------
    |
    |
    | Do we allow any user that is not authenticated on frontend to access backend?
    |
    | Default: true
    |
    */

    'allowBackendAccessBeforeAuth' => true,


    /*
    |--------------------------------------------------------------------------
    | Default Admin use role
    |--------------------------------------------------------------------------
    |
    |
    | The default role to assigned to admin user.
    |
    | Default: app.user
    |
    */

    'users_default_admin_role' => 'app.admin',


    /*
    |--------------------------------------------------------------------------
    | Default Registration Role
    |--------------------------------------------------------------------------
    |
    |
    | The default role to assign to registered users.
    |
    | Default: app.user
    |
    */

    'users_default_user_role' => 'app.user',


);