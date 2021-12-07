<?php

namespace App\Application\SqlScripts;

class CreateDatabaseScript
{
    private string $query;

    public function __construct(string $path)
    {
        $this->query = file_get_contents($path);
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }
}
