<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1', 'namespace' => 'Api\V1', 'middleware' => ['put']], function() use ($router){

    $router->get('version', ['as' => 'version', 'uses' => '\App\Http\Controllers\Controller@getVersion']);

    $router->group(['prefix' => 'users'], function() use ($router){
        $router->post('login', ['as' => 'login', 'uses' => 'UserController@login']);
        $router->post('register', ['as' => 'register', 'uses' => 'UserController@register']);
        $router->post('forgot', ['as' => 'forgot', 'uses' => 'UserController@forgot']);
        $router->post('check', ['as' => 'check', 'uses' => 'UserController@checkByEmail']);
        $router->group(['middleware' => ['auth:api']], function() use ($router){
            $router->get('profile', ['as' => 'profile.read', 'uses' => 'UserController@readProfile']);
            $router->put('profile', ['as' => 'profile.update', 'uses' => 'UserController@updateProfile']);
            $router->delete('profile', ['as' => 'profile.delete', 'uses' => 'UserController@deleteProfile']);
            $router->put('password', ['as' => 'password.update', 'uses' => 'UserController@updatePassword']);
        });
    });

    $router->group(['prefix' => 'notes', 'middleware' => ['auth:api']], function() use ($router) {
        $router->get('/', ['as' => 'notes.list', 'uses' => 'NoteController@list']);
        $router->post('/', ['as' => 'notes.create', 'uses' => 'NoteController@create']);
        $router->get('/search', ['as' => 'notes.search', 'uses' => 'NoteController@search']);
        $router->get('/{id}', ['as' => 'notes.read', 'uses' => 'NoteController@read']);
        $router->put('/{id}', ['as' => 'notes.update', 'uses' => 'NoteController@update']);
        $router->delete('/{id}', ['as' => 'notes.delete', 'uses' => 'NoteController@delete']);
    });

    $router->group(['prefix' => 'categories', 'middleware' => 'auth:api'], function() use($router) {
        $router->get('/', ['as' => 'categories.list', 'uses' => 'CategoryController@list']);
        $router->post('/', ['as' => 'categories.create', 'uses' => 'CategoryController@create']);
        $router->get('/{id}', ['as' => 'categories.read', 'uses' => 'CategoryController@read']);
        $router->put('/{id}', ['as' => 'categories.update', 'uses' => 'CategoryController@update']);
        $router->delete('/{id}', ['as' => 'categories.delete', 'uses' => 'CategoryController@delete']);
    });
});
