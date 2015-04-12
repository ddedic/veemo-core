<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Asset url path
	|--------------------------------------------------------------------------
	|
	| The path to asset, this config can be cdn host.
	| eg. http://cdn.domain.com
	|
	| Default: assets/   (relative to public folder)
	|
	*/

	'assetUrl' => 'assets/',

	/*
	|--------------------------------------------------------------------------
	| Theme Default
	|--------------------------------------------------------------------------
	|
	| If you don't set a theme when using a "Theme" class the default theme
	| will replace automatically.
	|
	*/

	'themeDefault' => 'default',


    /*
    |--------------------------------------------------------------------------
    | Theme Type Default
    |--------------------------------------------------------------------------
    |
    | If you don't set a theme when using a "Theme" class the default theme type
    | will replace automatically.
    |
    */

    'themeDefaultType' => 'frontend',

	/*
	|--------------------------------------------------------------------------
	| Layout Default
	|--------------------------------------------------------------------------
	|
	| If you don't set a layout when using a "Theme" class the default layout
	| will replace automatically.
	|
	*/

	'layoutDefault' => 'default',

	/*
	|--------------------------------------------------------------------------
	| Path to lookup theme
	|--------------------------------------------------------------------------
	|
	| The root path contains themes collections.
	|
	*/

	'themeDir' => [
        'frontend' => public_path('themes/frontend'),
        'backend'  => public_path('themes/backend')
    ],


    /*
    |--------------------------------------------------------------------------
    | Modules Path
    |--------------------------------------------------------------------------
    |
    | Here, you set the path (relative to your theme's root folder) for all
    | modules to reside.
    |
    */

    'modulesDir' => 'modules',



	/*
	|--------------------------------------------------------------------------
	| Listener from events
	|--------------------------------------------------------------------------
	|
	| You can hook a theme when event fired on activities
	| this is cool feature to set up a title, meta, default styles and scripts.
	|
	*/

	'events' => array(

		// Before all event, this event will effect for global.
		'before' => function($theme)
		{
			//$theme->setTitle('Something in global.');
		},

		// This event will fire as a global you can add any assets you want here.
		'asset' => function($asset)
		{
			// Preparing asset you need to serve after.
            $asset->cook('backbone', function($asset)
            {
                //$asset->add('backbone', '//cdnjs.cloudflare.com/ajax/libs/backbone.js/1.0.0/backbone-min.js');
                //$asset->add('underscorejs', '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.4/underscore-min.js');
            });

            // To use cook 'backbone' you can fire with 'serve' method.
            // Theme::asset()->serve('backbone');
		}

	)

);