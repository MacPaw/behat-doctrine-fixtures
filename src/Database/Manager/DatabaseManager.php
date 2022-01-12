<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database\Manager;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

abstract class DatabaseManager
{
    protected Connection $connection;

    protected bool $schemaCreated = false;

    protected string $cacheDir;

    private LoggerInterface $logger;

    public function __construct(Connection $connection, LoggerInterface $logger, string $cacheDir)
    {
        $this->connection = $connection;
        $this->logger = $logger;
        $this->cacheDir = $cacheDir;
    }

    abstract public function prepareSchema(): void;

    /**
     * @param array<string> $fixtures
     */
    abstract public function saveBackup(array $fixtures): void;

    /**
     * @param array<string> $fixtures
     */
    abstract public function loadBackup(array $fixtures): void;

    /**
     * @param array<string> $fixtures
     */
    abstract protected function getBackupFilename(array $fixtures): string;

    /**
     * @param array<string> $fixtures
     */
    public function backupExists(array $fixtures): bool
    {
        $backupFilename = $this->getBackupFilename($fixtures);
        if (file_exists($backupFilename)) {
            return true;
        }

        return false;
    }

    abstract protected function getDatabaseName(): string;

    /**
     * @param array<array> $context
     */
    protected function log(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }
}
