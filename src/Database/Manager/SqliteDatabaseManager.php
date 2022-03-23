<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database\Manager;

use BehatDoctrineFixtures\Database\Manager\ConsoleManager\SqliteConsoleManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Psr\Log\LoggerInterface;
use Throwable;

class SqliteDatabaseManager extends DatabaseManager
{
    private SqliteConsoleManager $consoleManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        SqliteConsoleManager $consoleManager,
        EntityManagerInterface $entityManager,
        Connection $connection,
        LoggerInterface $logger,
        string $cacheDir,
        string $connectionName
    ) {
        parent::__construct($connection, $logger, $cacheDir, $connectionName);
        $this->consoleManager = $consoleManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @param array<string> $fixtures
     */
    public function saveBackup(array $fixtures): void
    {
        $databasePath = $this->getDatabaseName();
        $backupFilename = $this->getBackupFilename($fixtures);

        $this->consoleManager->copy($databasePath, $backupFilename);

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
        $backupFileName = $this->getBackupFilename($fixtures);
        $databasePath = $this->getDatabaseName();

        $this->entityManager->clear();
        $this->connection->close();

        $this->consoleManager->copy($backupFileName, $databasePath);
        $this->consoleManager->changeMode($databasePath, 0666);

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
        $this->saveBackup([]);
    }

    public function createSchema(): void
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schema = $schemaTool->getSchemaFromMetadata($metadata);
        $this->adaptDatabaseSchemaToSqlite($schema);

        $dropSchemaSql = $schema->toDropSql($this->connection->getDatabasePlatform());
        $createSchemaSql = $schema->toSql($this->connection->getDatabasePlatform());

        foreach ($dropSchemaSql as $sql) {
            try {
                $this->connection->executeStatement($sql);
            } catch (Throwable $exception) {
            }
        }

        foreach ($createSchemaSql as $sql) {
            $this->connection->executeStatement($sql);
        }

        $this->schemaCreated = true;
        $this->log(sprintf('Schema created for %s connection', $this->connectionName));
    }

    private function adaptDatabaseSchemaToSqlite(Schema $schema): void
    {
        foreach ($schema->getTables() as $table) {
            foreach ($table->getColumns() as $column) {
                if ($column->hasCustomSchemaOption('collation')) {
                    $column->setCustomSchemaOptions(array_diff_key(
                        $column->getCustomSchemaOptions(),
                        ['collation' => true]
                    ));
                }
            }
        }
    }

    /**
     * @param array<string> $fixtures
     */
    protected function getBackupFilename(array $fixtures): string
    {
        $databaseName = $this->getDatabaseName();

        return sprintf('%s_%s.sql', $databaseName, md5(serialize($fixtures)));
    }

    protected function getDatabaseName(): string
    {
        return $this->connection->getParams()['path'];
    }
}
