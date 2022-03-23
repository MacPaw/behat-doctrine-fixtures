<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database\Manager\ConsoleManager;

class PostgreConsoleManager
{
    private string $cacheDir;
    private string $runMigrationsCommand;

    public function __construct(string $cacheDir, string $runMigrationsCommand)
    {
        $this->cacheDir = $cacheDir;
        $this->runMigrationsCommand = $runMigrationsCommand;
    }

    public function createDump(
        string $dumpFilename,
        string $user,
        string $host,
        int $port,
        string $databaseName,
        ?string $password = null,
        ?string $additionalParams = null
    ): void {
        $appendStderrFile = "2> {$this->cacheDir}/pg_dump_log.txt";
        $password = $password === null
            ? ''
            : "PGPASSWORD='{$password}'";

        // phpcs:disable
        exec("{$password} pg_dump -U{$user} -h{$host} -p{$port} {$additionalParams} {$databaseName} > {$dumpFilename} {$appendStderrFile}");
        // phpcs:enable
    }

    public function loadDump(
        string $dumpFilename,
        string $user,
        string $host,
        int $port,
        string $databaseName,
        ?string $password = null
    ): void {
        $password = "PGPASSWORD='{$password}'";

        exec("{$password} psql -U{$user} -h{$host} -p{$port} {$databaseName} < {$dumpFilename}");
    }

    public function runMigrations(): void
    {
        exec($this->runMigrationsCommand);
    }

    public function createDatabase(string $connectionName): void
    {
        // phpcs:disable
        exec(sprintf('bin/console d:d:drop --connection=%s --env=test --force --if-exists --no-interaction', $connectionName));
        exec(sprintf('bin/console d:d:create --connection=%s --env=test --if-not-exists --no-interaction', $connectionName));
        // phpcs:enable
    }
}
