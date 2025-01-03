<?php

namespace App\Controllers;

use Trophphic\Core\Controller;
use Trophphic\Core\Request;
use Trophphic\Core\Response;

class HomeController extends Controller
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