<?php

namespace Trophphic\Core;

use Trophphic\Core\Session\SessionInterface;
use Trophphic\Core\Session\SessionManager;

class Response
{
    private SessionInterface $session;

    public function __construct()
    {
        $this->session = SessionManager::getInstance();
    }

    public function withErrors(array $errors): self
    {
        $this->session->flash('errors', $errors);
        return $this;
    }

    public function withInput(array $input): self
    {
        foreach ($input as $key => $value) {
            $this->session->flash("old_$key", $value);
        }
        return $this;
    }

    public function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    public function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    public function json(array $data, int $statusCode = 200): void
    {
        $this->setStatusCode($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
} 