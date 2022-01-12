<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database\Manager\ConsoleManager;

class PostgreConsoleManager
{
    private string $cacheDir;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function createDump(
        string $dumpFilename,
        string $user,
        string $host,
        string $port,
        string $databaseName,
        ?string $password = null,
        ?string $additionalParams = null
    ): void {
        $appendStderrFile = "2> {$this->cacheDir}/pg_dump_log.txt";
        $password = $password === null
            ? ''
            : "PGPASSWORD='{$password}'";

        exec("{$password} pg_dump -U{$user} -h{$host} -p{$port} {$additionalParams} {$databaseName} > 
            {$dumpFilename} {$appendStderrFile}");
    }

    public function loadDump(
        string $dumpFilename,
        string $user,
        string $host,
        string $port,
        string $databaseName,
        ?string $password = null
    ): void {
        $password = "PGPASSWORD='{$password}'";

        exec("{$password} psql -U{$user} -h{$host} -p{$port} {$databaseName} < {$dumpFilename}");
    }

    public function runMigrations(): void
    {
        exec('bin/console d:mi:mi --no-interaction');
    }

    public function createDatabase(): void
    {
        exec('bin/console d:d:create');
    }
}
