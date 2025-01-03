<?php

namespace App\Controllers;

use Trophphic\Core\TrophphicController;
use Trophphic\Core\Request;
use Trophphic\Core\Response;

class HomeController extends TrophphicController
{
    public function index(Request $request, Response $response): string
    {
        return $this->render('home', [
            'name' => 'Trophphic Framework'
        ]);
    }

    public function about(Request $request, Response $response): string
    {
        return $this->render('about');
    }
} 