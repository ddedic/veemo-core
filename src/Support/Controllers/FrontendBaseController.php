<?php namespace Veemo\Core\Support\Controllers;



class FrontendBaseController extends BaseController {

    public function __construct()
    {
        $this->theme = app('veemo.theme')->frontend()->uses(config('veemo.core.frontendTheme'));
    }


    protected function getThemeName()
    {
        return $this->theme->getThemeName();
    }


    public function test()
    {

        // kodio push/pull test

        return $this->theme
            //->layout('default')
            ->setVar('Testić')
            ->prependTitle('Frontend test - ')
            ->view('testView')
            ->render();
    }



}