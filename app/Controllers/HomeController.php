<?php

namespace App\Controllers;

use Trophphic\Core\TrophphicController;
use Trophphic\Core\Request;
use Trophphic\Core\Response;
use Trophphic\Core\Session\SessionManager;

class HomeController extends TrophphicController
{
    public function index(Request $request, Response $response): string
    {
        $session = SessionManager::getInstance();
        
        // Set session data
        $session->set('user_id', 1);
        
        // Set flash message
        $session->flash('message', 'Welcome back!');
        
        // Get session data
        $userId = $session->get('user_id');
        
        // Get flash message
        $message = $session->getFlash('message');
        
        // Regenerate session ID (useful after login)
        // $session->regenerate();
        //$session->destroy();

        return $this->render('home', [
            'name' => 'Trophphic Framework'
        ]);
    }

    public function about(Request $request, Response $response): string
    {
        return $this->render('about');
    }
} 