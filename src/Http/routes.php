<?php


/*
|--------------------------------------------------------------------------
| Default Frontend Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the frontend routes for the module.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(['namespace' => 'App\Modules\Homepage\Http\Controllers\Frontend'], function () {

    Route::get('/', ['uses' => 'FrontendController@getHomepage', 'as' => 'frontend.homepage']);

});


/*
|--------------------------------------------------------------------------
| Default Backend Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the backend routes for the module.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(['prefix' => config('veemo.core.backendPrefix')], function () {


    Route::get('/', ['as' => 'backend', 'middleware' => 'auth.backend'], function() {
        return redirect()->route('backend.dashboard');
    });


});


/*
|--------------------------------------------------------------------------
| Default API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the API routes for the module.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(['prefix' => 'api'], function () {

    // API routes

});