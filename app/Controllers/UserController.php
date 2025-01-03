<?php

namespace App\Controllers;

use App\Models\User;
use Trophphic\Core\TrophphicController;
use Trophphic\Core\Request;
use Trophphic\Core\Response;
use Trophphic\Core\Security\CSRF;
use Trophphic\Core\Security\XSS;
use Trophphic\Core\Form\Validator;

class UserController extends TrophphicController
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index(Request $request, Response $response): string
    {
        $this->logger->info('Displaying users list');
        $users = $this->userModel->all();
        return $this->render('users/index', ['users' => $users]);
    }

    public function create(Request $request, Response $response): string
    {
        $this->logger->info('Displaying user creation form');
        return $this->render('users/create');
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            // Validate CSRF token
            if (!CSRF::validateToken($request->input('_csrf_token'))) {
                throw new \Exception('Invalid CSRF token');
            }

            // Clean input data
            $data = XSS::clean($request->all());

            // Validate input
            $validator = new Validator($data, [
                'name' => 'required|min:2',
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed'
            ]);

            if (!$validator->validate()) {
                return $this->redirectBack()
                    ->withErrors($validator->errors())
                    ->withInput($request->except(['password', 'password_confirmation']));
            }

            $this->logger->info('Creating new user', ['email' => $data['email']]);
            
            if (!empty($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if ($this->userModel->create($data)) {
                $this->logger->info('User created successfully', ['email' => $data['email']]);
                $response->redirect('/users');
            }
        } catch (\Exception $e) {
            $this->logger->error('Failed to create user', [
                'email' => $data['email'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function edit(Request $request, Response $response, $id): string
    {
        $this->logger->info('Displaying user edit form', ['id' => $id]);
        $user = $this->userModel->find($id);
        return $this->render('users/edit', ['user' => $user]);
    }

    public function update(Request $request, Response $response, $id): void
    {
        $data = $request->getBody();
        $this->logger->info('Updating user', ['id' => $id]);
        
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        try {
            if ($this->userModel->update($id, $data)) {
                $this->logger->info('User updated successfully', ['id' => $id]);
                $response->redirect('/users');
            }
        } catch (\Exception $e) {
            $this->logger->error('Failed to update user', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function delete(Request $request, Response $response, $id): void
    {
        $this->logger->info('Deleting user', ['id' => $id]);
        
        try {
            if ($this->userModel->delete($id)) {
                $this->logger->info('User deleted successfully', ['id' => $id]);
                $response->redirect('/users');
            }
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete user', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
} 