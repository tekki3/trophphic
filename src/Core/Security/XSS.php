<?php

namespace Trophphic\Core\Security;

class XSS
{
    public static function clean($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'clean'], $data);
        }

        if (is_string($data)) {
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }

        return $data;
    }
} 