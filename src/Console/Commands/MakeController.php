<?php

namespace Trophphic\Console\Commands;

use Trophphic\Console\Command;

class MakeController extends Command
{
    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->error("Controller name is required");
            return;
        }

        $controllerName = ucfirst($name) . 'Controller';
        $path = __DIR__ . "/../../../app/Controllers/$controllerName.php";

        if (file_exists($path)) {
            $this->error("Controller already exists: $controllerName");
            return;
        }

        $stub = $this->getStub('controller');
        $content = str_replace(
            ['{{controllerName}}'],
            [$controllerName],
            $stub
        );

        $this->createDirectory(dirname($path));
        file_put_contents($path, $content);

        $this->success("Controller created successfully: $controllerName");
    }
} 