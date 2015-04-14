<?php namespace Veemo\Core\Support\Controllers;



class BackendBaseController extends BaseController {


    public function __construct()
    {
        $this->theme = app('veemo.theme')->backend()->uses(config('veemo.core.backendTheme'));



    }


    protected function getThemeName()
    {
        return $this->theme->getThemeName();
    }


}