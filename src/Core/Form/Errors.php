<?php

namespace Trophphic\Core\Form;

class Errors
{
    private array $errors;

    public function __construct(array $errors = [])
    {
        $this->errors = $errors;
    }

    public function has(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function all(): array
    {
        return $this->errors;
    }

    public function any(): bool
    {
        return !empty($this->errors);
    }
}