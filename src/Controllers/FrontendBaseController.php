<?php namespace Veemo\Core\Controllers;



class FrontendBaseController extends BaseController {

    public function __construct()
    {
        $this->theme = app('veemo.theme')->frontend()->uses(config('veemo.core.frontendTheme'));

        $this->middleware('frontend.force.ssl');
    }


    protected function getThemeName()
    {
        return $this->theme->getThemeName();
    }



}