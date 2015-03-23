<?php namespace Veemo\Core\Themes;

use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config.
        $this->publishes([
            __DIR__ . '/../Publish/Config/themes.php' => config_path('themes.php'),
        ]);

        // \Debugbar::info('Themes loaded');

    }

    /**
     * Register service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../Publish/Config/themes.php', 'veemo.themes'
        );

        // Register theme manager
        $this->registerThemeManager();

        // Register theme
        //$this->registerTheme();

        // Register commands
        //$this->registerThemeGenerator();
        //$this->registerThemeDestroy();

        // Assign commands.
        $this->commands(
            'veemo.theme.create',
            'veemo.theme.destroy',
            'veemo.theme.publish'
        );
    }


    public function registerThemeManager()
    {
        $this->app['theme.manager'] = $this->app->share(function ($app) {
            $adapter = new Adapters\FileManagerAdapter($app['files'], $app['config']);

            return new ThemeManager($adapter, $app['config']);
        });
    }



    /**
     * Register theme provider.
     *
     * @return void
     */
    public function registerTheme()
    {
        $this->app['theme'] = $this->app->share(function ($app) {
            return new Theme($app['config'], $app['events'], $app['view'], $app['asset'], $app['files']);
        });
    }


    /**
     * Register generator of theme.
     *
     * @return void
     */
    public function registerThemeGenerator()
    {
        $this->app['veemo.theme.create'] = $this->app->share(function ($app) {
            return new Commands\ThemeGeneratorCommand($app['config'], $app['files']);
        });
    }


    /**
     * Register theme destroy.
     *
     * @return void
     */
    public function registerThemeDestroy()
    {
        $this->app['veemo.theme.destroy'] = $this->app->share(function ($app) {
            return new Commands\ThemeDestroyCommand($app['config'], $app['files']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('theme');
    }

}