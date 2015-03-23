<?php namespace Veemo\Core;

use App, Config, Lang, View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;


class CoreServiceProvider extends ServiceProvider
{

    /**
     * @var bool $defer Indicates if loading of the provider is deferred.
     */
    protected $defer = false;


    /**
     * Register the Core module service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerNamespaces();

        $this->registerModules();

        $this->registerThemes();

        $this->registerConsoleCommands();


    }


    public function boot()
    {
        \Debugbar::info('Veemo Core Service Provider loaded');
    }


    protected function registerModules()
    {
        $this->app->register('Veemo\Core\Modules\ModulesServiceProvider');
        AliasLoader::getInstance()->alias('Module', 'Veemo\Core\Modules\Facades\Module');
    }


    protected function registerThemes()
    {
        $this->app->register('Veemo\Core\Themes\ThemeServiceProvider');
        AliasLoader::getInstance()->alias('Theme', 'Veemo\Core\Themes\Facades\Theme');
    }


    /**
     * Register the Core module resource namespaces.
     *
     * @return void
     */
    protected function registerNamespaces()
    {

        //

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
