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
    private string $migrationsTable;
    private bool $preserveMigrationsData;

    public function __construct(
        PostgreConsoleManager $consoleManager,
        ORMExecutor $executor,
        Connection $connection,
        LoggerInterface $logger,
        string $migrationsTable,
        string $cacheDir,
        string $connectionName,
        bool $preserveMigrationsData
    ) {
        parent::__construct($connection, $logger, $cacheDir, $connectionName);
        $this->consoleManager = $consoleManager;
        $this->executor = $executor;
        $this->migrationsTable = $migrationsTable;
        $this->preserveMigrationsData = $preserveMigrationsData;
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
        $password = $this->getConnectionParams()['password'];
        $user = $this->getConnectionParams()['user'];
        $host = $this->getConnectionParams()['host'];
        $port = $this->getConnectionParams()['port'];

        # Needed for optimization
        $additionalParams = sprintf('--no-comments --disable-triggers --data-only -T %s', $this->migrationsTable);

        $this->consoleManager->createDump(
            $backupFilename,
            $user,
            $host,
            $port,
            $databaseName,
            $password,
            $additionalParams
        );

        $this->log(
            sprintf('Database backup saved to file %s for %s connection', $backupFilename, $this->connectionName),
            ['fixtures' => $fixtures]
        );
    }

    /**
     * @param array<string> $fixtures
     */
    public function loadBackup(array $fixtures): void
    {
        if (!$this->schemaCreated) {
            $this->createSchema();
        }

        $this->dropData();

        $backupFilename = $this->getBackupFilename($fixtures);
        $databaseName = $this->getDatabaseName();
        $password = $this->getConnectionParams()['password'];
        $user = $this->getConnectionParams()['user'];
        $host = $this->getConnectionParams()['host'];
        $port = $this->getConnectionParams()['port'];

        $this->consoleManager->loadDump($backupFilename, $user, $host, $port, $databaseName, $password);

        $this->log(
            sprintf('Database backup loaded for %s connection', $this->connectionName),
            ['fixtures' => $fixtures]
        );
    }

    public function prepareSchema(): void
    {
        if ($this->schemaCreated) {
            $this->loadBackup([]);

            return;
        }

        $this->createSchema();

        if (!$this->preserveMigrationsData) {
            $this->dropData();
        }

        $this->saveBackup([]);
    }

    public function createSchema(): void
    {
        $this->recreateDatabase();
        $this->runMigrations();

        $this->schemaCreated = true;
        $this->log(sprintf('Schema created for %s connection', $this->connectionName));
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

        /** @var array{sequence_name: string, start_value?: string} $sequence */
        foreach ($sequences as $sequence) {
            $this->connection->executeStatement(sprintf(
                'ALTER SEQUENCE %s RESTART WITH %s',
                $sequence['sequence_name'],
                $sequence['start_value'] ?? '1'
            ));
        }
    }

    /**
     * @param array<string> $fixtures
     */
    protected function getBackupFilename(array $fixtures): string
    {
        $databaseName = $this->getDatabaseName();

        return sprintf(
            '%s/%s_%s_%s.sql',
            $this->cacheDir,
            $this->connectionName,
            $databaseName,
            md5(serialize($fixtures))
        );
    }

    private function recreateDatabase(): void
    {
        $this->consoleManager->dropDatabase($this->connectionName);
        $this->consoleManager->createDatabase($this->connectionName);

        $this->log(sprintf('Database created for %s connection', $this->connectionName));
    }

    private function runMigrations(): void
    {
        $this->consoleManager->runMigrations();

        $this->log(sprintf('Migrations ran for %s connection', $this->connectionName));
    }

    protected function getDatabaseName(): string
    {
        return $this->getConnectionParams()['dbname'];
    }

    private function getConnectionParams(): array
    {
        /** @var array{dbname: string, password: string, user: string, host: string, port: int} $connectionParams */
        $connectionParams = $this->connection->getParams();

        return $connectionParams;
    }
}
