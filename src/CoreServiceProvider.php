<?php namespace Veemo\Core;

use App, Config, Lang, View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Bus\DispatchesCommands;

use Veemo\Core\Command\ConfigureCommandBus;
use Veemo\Core\Command\CheckInstaller;
use Veemo\Core\Command\RegisterModules;
use Veemo\Core\Command\RegisterDefaultRoutes;
use Veemo\Core\Command\BuildMainBackendMenu;

class CoreServiceProvider extends ServiceProvider
{

    use DispatchesCommands;


    /**
     * @var bool $defer Indicates if loading of the provider is deferred.
     */
    protected $defer = false;


    /**
     * The filters base class name.
     *
     * @var array
     */
    protected $middlewares = [
        'backend.force.ssl'       => 'BackendForceSslMiddleware',
        'frontend.force.ssl'      => 'FrontendForceSslMiddleware'
    ];



    public function boot()
    {
        // Publish config.
        $this->publishes([
            __DIR__ . '/Publish/Config/core.php' => config_path('veemo/core.php'),
        ]);


        // ON BOOT
        $this->dispatch(new ConfigureCommandBus());
        $this->dispatch(new CheckInstaller());



        // ON BOOTED
        $this->app->booted(function ($app) {

            $this->dispatch(new RegisterModules());
            $this->dispatch(new RegisterDefaultRoutes());
            $this->dispatch(new BuildMainBackendMenu());

        });

    }



    /**
     * Register the Core module service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Publish/Config/core.php', 'veemo.core'
        );


        /**
         * This is used often, make it a singleton.
         */
        $this->app->singleton(
            'Veemo\Core\Model\BaseEloquentObserver',
            'Veemo\Core\Model\BaseEloquentObserver'
        );


        // AFTER SETTINGS MODULE
        /**
         * Register our own exception handler. This will let
         * us intercept exceptions and make them pretty if
         * not in debug mode.
         */
        //$this->app->bind(
        //    'Illuminate\Contracts\Debug\ExceptionHandler',
        //    'Veemo\Core\Exception\ExceptionHandler'
        //);


        $this->registerServices();

        $this->registerAuth();

        $this->registerModules();

        $this->registerThemes();

        $this->registerConsoleCommands();

        $this->registerHelpers();

        $this->setupOverrides();

        $this->app->booted(function ($app) {

            $this->registerMiddlewares($app->router);

        });


    }


    protected function registerServices()
    {
        $aliases = AliasLoader::getInstance();


        // HTML + FORM
        $this->app->register('Collective\Html\HtmlServiceProvider');

        // FORM
        $aliases->alias(
            'Form',
            'Collective\Html\FormFacade'
        );

        // HTML
        $aliases->alias(
            'Html',
            'Collective\Html\HtmlFacade'
        );


        // FLASH Notifications
        $this->app->register('Laracasts\Flash\FlashServiceProvider');

        // Flash facade
        $aliases->alias(
            'Flash',
            'Laracasts\Flash\Flash'
        );


        // Caffeinated Menus Service Provider
        $this->app->register('Caffeinated\Menus\MenusServiceProvider');

        // Caffeinated Menus Facade
        $aliases->alias(
            'Menu',
            'Caffeinated\Menus\Facades\Menu'
        );

    }


    protected function registerAuth()
    {
        $this->app->register('Veemo\Auth\AuthServiceProvider');
    }

    protected function registerModules()
    {
        $this->app->register('Veemo\Modules\ModulesServiceProvider');
        AliasLoader::getInstance()->alias('Modules', 'Veemo\Modules\Facades\Modules');

        /*
        $this->app->booted(function ($app) {

           $app->make('veemo.modules')->registerModules();

        });
        */
    }


    protected function registerThemes()
    {
        $this->app->register('Veemo\Themes\ThemeServiceProvider');
        AliasLoader::getInstance()->alias('Theme', 'Veemo\Themes\Facades\Theme');
    }



    protected function registerHelpers()
    {
        foreach (glob(__DIR__ . '/Helpers/*Helper.php') as $filename){
            require_once($filename);
        }
    }



    public function registerMiddlewares(Router $router)
    {
        foreach ($this->middlewares as $name => $middleware) {
            $class = "Veemo\\Core\\Http\\Middleware\\{$middleware}";
            $router->middleware($name, $class);
        }
    }

    public function setupOverrides()
    {

        // Flash CSRF exception and redirect back to form
        $this->app->bind(
            'App\Http\Middleware\VerifyCsrfToken',
            'Veemo\Core\Http\Middleware\VerifyCsrfToken'
        );

    }



    /**
     * Register the package console commands.
     *
     * @return void
     */
    protected function registerConsoleCommands()
    {
        $this->registerInstallCommand();

        $this->commands([
            'veemo.install'
        ]);
    }


    /**
     * Register the "veemo:install" console command.
     *
     * @return Console\InstallCommand
     */
    protected function registerInstallCommand()
    {
        $this->app->bindShared('veemo.install', function ($app) {
            $handler = new Handlers\InstallHandler();
            return new Console\InstallCommand($handler);
        });
    }


}
