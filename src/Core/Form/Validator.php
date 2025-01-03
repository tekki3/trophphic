<?php

namespace Trophphic\Core\Form;

use Trophphic\Core\Logger;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    private Logger $logger;

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->logger = Logger::getInstance();
    }

    public function validate(): bool
    {
        foreach ($this->rules as $field => $ruleSet) {
            $rules = is_string($ruleSet) ? explode('|', $ruleSet) : $ruleSet;
            
            foreach ($rules as $rule) {
                $this->validateField($field, $rule);
            }
        }

        return empty($this->errors);
    }

    private function validateField(string $field, string $rule): void
    {
        $value = $this->data[$field] ?? null;
        
        // Parse rule with parameters
        if (str_contains($rule, ':')) {
            [$ruleName, $parameter] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $parameter = null;
        }

        $method = 'validate' . ucfirst($ruleName);
        if (method_exists($this, $method)) {
            $this->$method($field, $value, $parameter);
        }
    }

    private function validateRequired(string $field, $value): void
    {
        if (empty($value) && $value !== '0') {
            $this->addError($field, "The {$field} field is required.");
        }
    }

    private function validateEmail(string $field, $value): void
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "The {$field} must be a valid email address.");
        }
    }

    private function validateMin(string $field, $value, string $min): void
    {
        if (!empty($value) && strlen($value) < (int)$min) {
            $this->addError($field, "The {$field} must be at least {$min} characters.");
        }
    }

    private function validateMax(string $field, $value, string $max): void
    {
        if (!empty($value) && strlen($value) > (int)$max) {
            $this->addError($field, "The {$field} may not be greater than {$max} characters.");
        }
    }

    private function validateConfirmed(string $field, $value): void
    {
        $confirmation = $this->data[$field . '_confirmation'] ?? null;
        if ($value !== $confirmation) {
            $this->addError($field, "The {$field} confirmation does not match.");
        }
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }
} 