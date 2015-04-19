<?php namespace Veemo\Core\Support\Controllers;



class BackendBaseController extends BaseController {


    public function __construct()
    {

        // Setup backend theme
        $this->theme = app('veemo.theme')
            ->backend()
            ->uses(config('veemo.core.backendTheme'));

        // Setup middleware
        $this->middleware('backend.force.ssl');
        $this->middleware('auth.backend', ['except' => ['getLogin', 'postLogin']]);

    }


    protected function getThemeName()
    {
        return $this->theme->getThemeName();
    }


}