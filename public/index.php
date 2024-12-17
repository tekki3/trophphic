<?php

require_once '../vendor/autoload.php';


use Trophphic\Core\Env;
Env::load('../.env');

require_once '../App/routes.php'; // Load the routes file

session_set_save_handler(new Trophphic\Core\SessionHandler(), true);
session_start();
Trophphic\Core\Security::generateCsrfToken();

$app = new Trophphic\Core\App();



