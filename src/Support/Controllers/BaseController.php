<?php namespace Veemo\Core\Support\Controllers;


use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class BaseController extends Controller {

    use DispatchesCommands, ValidatesRequests;


    /**
     * @theme \Veemo\Core\Themes\Theme
     */
    protected $theme = 'test';

}



