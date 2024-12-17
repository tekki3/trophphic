<?php

use Trophphic\Core\Router;

Router::get('/', 'HomeController@index');
Router::get('/about', 'HomeController@about');
Router::post('/submit', 'FormController@submit');

