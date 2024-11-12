<?php
use App\Controllers\HomeController;
use App\Controllers\UserController;
$router = new Router();

$router->get('/', 'HomeController@index')->name('home');
$router->get('/about', 'HomeController@about')->name('about');


$router->group('/users', function($router) {
    $router->get('/', 'UserController@index')->name('user.index');
    $router->get('/{id}', 'UserController@show')->name('users.show');
});