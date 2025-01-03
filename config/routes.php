<?php

use App\Controllers\HomeController;
use App\Controllers\UserController;
use Trophphic\Core\Bootstrap;

$app = Bootstrap::getInstance();

// Home routes
$app->router->get('/', [HomeController::class, 'index']);
$app->router->get('/about', [HomeController::class, 'about']);

// User routes
$app->router->get('/users', [UserController::class, 'index']);
$app->router->get('/users/create', [UserController::class, 'create']);
$app->router->post('/users/store', [UserController::class, 'store']);
$app->router->get('/users/edit/{id}', [UserController::class, 'edit']);
$app->router->post('/users/update/{id}', [UserController::class, 'update']);
$app->router->post('/users/delete/{id}', [UserController::class, 'delete']); 