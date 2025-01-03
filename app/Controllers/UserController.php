<?php

namespace App\Controllers;

use App\Models\User;
use Trophphic\Core\TrophphicController;
use Trophphic\Core\Request;
use Trophphic\Core\Response;

class UserController extends TrophphicController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index(Request $request, Response $response): string
    {
        $users = $this->userModel->all();
        return $this->render('users/index', ['users' => $users]);
    }

    public function create(Request $request, Response $response): string
    {
        return $this->render('users/create');
    }

    public function store(Request $request, Response $response): void
    {
        $data = $request->getBody();
        
        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if ($this->userModel->create($data)) {
            $response->redirect('/users');
        }
    }

    public function edit(Request $request, Response $response, $id): string
    {
        $user = $this->userModel->find($id);
        return $this->render('users/edit', ['user' => $user]);
    }

    public function update(Request $request, Response $response, $id): void
    {
        $data = $request->getBody();
        
        // Only update password if provided
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if ($this->userModel->update($id, $data)) {
            $response->redirect('/users');
        }
    }

    public function delete(Request $request, Response $response, $id): void
    {
        if ($this->userModel->delete($id)) {
            $response->redirect('/users');
        }
    }
} 