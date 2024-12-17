<?php

namespace Trophphic\Core;

use Trophphic\Core\Router;

class App {
    public function __construct() {
        Router::handleRequest();
    }
}