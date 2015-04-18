<?php namespace Veemo\Core\Support\Controllers;



class BackendBaseController extends BaseController {


    public function __construct()
    {
        $this->theme = app('veemo.theme')
            ->backend()
            ->uses(config('veemo.core.backendTheme'));


        $this->middleware('auth.backend', ['except' => ['getLogin', 'postLogin']]);

    }


    protected function getThemeName()
    {
        return $this->theme->getThemeName();
    }


}