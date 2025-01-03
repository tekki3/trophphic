<?php

namespace Trophphic\Console\Commands;

class MakeView
{
    public function handle(string $name): void
    {
        $viewPath = "app/Views/{$name}.php";
        $directory = dirname($viewPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (file_exists($viewPath)) {
            echo "View already exists: $viewPath\n";
            return;
        }

        $template = <<<PHP
<!DOCTYPE html>
<html>
<head>
    <title><?= \$title ?? 'Page Title' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1><?= \$title ?? 'Page Title' ?></h1>
        
        <!-- Content goes here -->
        
    </div>
</body>
</html>
PHP;

        file_put_contents($viewPath, $template);
        echo "View created: $viewPath\n";
    }
} 