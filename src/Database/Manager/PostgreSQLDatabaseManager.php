<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database\Manager;

use BehatDoctrineFixtures\Database\Manager\ConsoleManager\PostgreConsoleManager;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class PostgreSQLDatabaseManager extends DatabaseManager
{
    private ORMExecutor $executor;
    private PostgreConsoleManager $consoleManager;

    public function __construct(
        PostgreConsoleManager $consoleManager,
        ORMExecutor $executor,
        Connection $connection,
        LoggerInterface $logger,
        string $cacheDir
    ) {
        parent::__construct($connection, $logger, $cacheDir);
        $this->consoleManager = $consoleManager;
        $this->executor = $executor;
    }

    /**
     * @param array<string> $fixtures
     */
    public function saveBackup(array $fixtures): void
    {
        $backupFilename = $this->getBackupFilename($fixtures);

        if (file_exists($backupFilename)) {
            return;
        }

        $databaseName = $this->getDatabaseName();
        $password = $this->connection->getParams()['password'];
        $user = $this->connection->getParams()['user'];
        $host = $this->connection->getParams()['host'];
        $port = $this->connection->getParams()['port'];
        # Needed for optimization
        $additionalParams = "--exclude-table=migration_versions --no-comments --disable-triggers --data-only";

        $this->consoleManager->createDump(
            $backupFilename,
            $user,
            $host,
            $port,
            $databaseName,
            $password,
            $additionalParams
        );

        $this->log(sprintf('Database backup saved to file %s', $backupFilename), ['fixtures' => $fixtures]);
    }

    /**
     * @param array<string> $fixtures
     */
    public function loadBackup(array $fixtures): void
    {
        $this->dropData();

        if (empty($fixtures)) {
            return;
        }

        $backupFilename = $this->getBackupFilename($fixtures);
        $databaseName = $this->getDatabaseName();
        $password = $this->connection->getParams()['password'];
        $user = $this->connection->getParams()['user'];
        $host = $this->connection->getParams()['host'];
        $port = $this->connection->getParams()['port'];

        $this->consoleManager->loadDump($backupFilename, $user, $host, $port, $databaseName, $password);

        $this->log('Database backup loaded');
    }

    public function prepareSchema(): void
    {
        if (!$this->schemaCreated) {
            $this->createSchema();
        }

        $this->dropData();
    }

    public function createSchema(): void
    {
        $this->createDatabase();

        $this->consoleManager->runMigrations();

        $this->schemaCreated = true;
        $this->log('Schema created');
    }

    private function dropData(): void
    {
        $this->executor->purge();
        $this->restartSequences();
    }

    private function restartSequences(): void
    {
        $sequences = $this->connection->executeQuery('SELECT * FROM information_schema.sequences')
            ->fetchAllAssociative();

        foreach ($sequences as $sequence) {
            $this->connection->executeStatement(
                sprintf('ALTER SEQUENCE %s RESTART WITH 1', $sequence['sequence_name'])
            );
        }
    }

    /**
     * @param array<string> $fixtures
     */
    protected function getBackupFilename(array $fixtures): string
    {
        $databaseName = $this->getDatabaseName();

        return sprintf('%s/%s_%s.sql', $this->cacheDir, $databaseName, md5(serialize($fixtures)));
    }

    private function createDatabase(): void
    {
        $this->consoleManager->createDatabase();

        $this->log('Database created');
    }

    protected function getDatabaseName(): string
    {
        return $this->connection->getParams()['dbname'];
    }
}
