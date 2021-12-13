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


$router->get('/test', 'ExampleController@index');
$router->group(['prefix' => 'api'], function () use ($router) {
   // Matches "/api/register
   $router->post('register', 'AuthController@regis');
    $router->post('login', 'AuthController@login');
 //$router->get('test', 'TransactionController@test');
});

$router->group(['prefix' => 'transaction', 'middleware' => 'auth'], function () use ($router) {
    $router->get('/', 'TransactionController@test');
    $router->get('/merchant', 'TransactionController@index');
    $router->get('/outlet', 'TransactionController@indexoutlet');

});