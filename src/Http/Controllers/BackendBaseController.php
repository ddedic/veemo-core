<?php namespace Veemo\Core\Http\Controllers;

use Menu;
use Auth;

class BackendBaseController extends BaseController {


    public function __construct()
    {
        // Setup backend theme
        $this->theme = app('veemo.theme')
            ->backend()
            ->uses(config('veemo.core.backendTheme'));

        $this->setupBackendMenu();

        // Setup middleware
        $this->middleware('backend.force.ssl');
        $this->middleware('auth.backend', ['except' => ['getLogin', 'postLogin', 'getEmail', 'postEmail', 'getReset', 'postReset']]);

    }


    protected function getThemeName()
    {
        return $this->theme->getThemeName();
    }


    protected function setupBackendMenu()
    {
        $backend_menu = Menu::get('backend_main');

        if (Auth::check())
        {
            // Filter by permissions
            $backend_menu->filter(function($item) {
                return (Auth::user()->can($item->data('permissions')) ?: false);
            });
        }


    }


}