<?php namespace Veemo\Core\Controllers;



class BackendBaseController extends BaseController {


    public function __construct()
    {

        // Setup backend theme
        $this->theme = app('veemo.theme')
            ->backend()
            ->uses(config('veemo.core.backendTheme'));

        // Setup middleware
        $this->middleware('backend.force.ssl');
        $this->middleware('auth.backend', ['except' => ['getLogin', 'postLogin', 'getEmail', 'postEmail', 'getReset', 'postReset']]);

    }


    protected function getThemeName()
    {
        return $this->theme->getThemeName();
    }


}