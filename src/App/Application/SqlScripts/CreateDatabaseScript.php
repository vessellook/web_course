<?php

namespace App\Application\SqlScripts;

use PDO;

class CreateDatabaseScript
{
    private string $query;

    public function __construct(string $path, private PDO $pdo)
    {
        $this->query = file_get_contents($path);
    }

    public function run(): bool
    {
        return boolval($this->pdo->query($this->query));
    }
}
