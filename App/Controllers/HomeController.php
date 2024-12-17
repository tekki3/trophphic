<?php

namespace Trophphic\App\Controllers;

use Trophphic\Core\TrophphicController;
use Trophphic\Core\Security;
use Trophphic\Core\Logger;

class HomeController extends TrophphicController {
    public function index() {
        if ($_ENV['LOGGING_ENABLED'] === 'true') {
            Logger::info("HomeController index method accessed.");
        }
        $this->view('home/index', ['title' => Security::sanitizeOutput('Home Page')]);
    }

    public function about() {
        if ($_ENV['LOGGING_ENABLED'] === 'true') {
            Logger::info("HomeController about method accessed.");
        }
        $this->view('home/about', ['title' => Security::sanitizeOutput('About Us')]);
    }
}

