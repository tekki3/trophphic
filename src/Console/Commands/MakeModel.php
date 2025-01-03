<?php

namespace Trophphic\Console\Commands;

use Trophphic\Console\Command;

class MakeModel extends Command
{
    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->error("Model name is required");
            return;
        }

        $modelName = ucfirst($name);
        $path = __DIR__ . "/../../../app/Models/$modelName.php";

        if (file_exists($path)) {
            $this->error("Model already exists: $modelName");
            return;
        }

        $stub = $this->getStub('model');
        $content = str_replace(
            ['{{modelName}}', '{{tableName}}'],
            [$modelName, strtolower($name) . 's'],
            $stub
        );

        $this->createDirectory(dirname($path));
        file_put_contents($path, $content);

        $this->success("Model created successfully: $modelName");
    }
} 