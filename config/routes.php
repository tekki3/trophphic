<?php

use App\Controllers\HomeController;
use Trophphic\Bootstrap;

$app = Bootstrap::getInstance();

$app->router->get('/', [HomeController::class, 'index']);
$app->router->get('/about', [HomeController::class, 'about']); 