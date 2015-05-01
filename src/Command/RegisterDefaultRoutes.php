<?php namespace Veemo\Core\Command;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Bus\SelfHandling;


class RegisterDefaultRoutes implements SelfHandling
{

    /**
     * Handle the command.
     */
    public function handle(Repository $config)
    {
        $modules = app('veemo.modules');


        if ($modules->getManager()->isInstalled('homepage')) {
            // frontend.homepage route
            app('router')->get('/', ['uses' => 'App\Modules\Core\Homepage\Http\Controllers\Frontend\FrontendController@getHomepage', 'as' => 'frontend.homepage']);
        }


        if ($modules->getManager()->isInstalled('dashboard'))
        {
            // backend route
            app('router')->get($config->get('veemo.core.backendPrefix'), function () {
                return redirect()->route('backend.dashboard');
            });
        }


    }
}
