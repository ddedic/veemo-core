<?php namespace Veemo\Core;

use App, Config, Lang, View;
use Illuminate\Support\ServiceProvider;

use Caffeinated\Modules\ModulesServiceProvider;


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



	}


	public function boot()
	{
		\Debugbar::info('Veemo Core Service Provider loaded');
	}

		

	protected function registerModules()
	{

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



}
