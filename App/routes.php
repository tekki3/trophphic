<?php
use Trophphic\Core\Router;


Router::get('/', 'HomeController@index');
Router::get('/about', 'HomeController@about');
Router::post('/submit', 'FormController@submit');

Router::setNotFoundView($_ENV['APP_FILE_LOC'] . '/App/views/error/404.php');




