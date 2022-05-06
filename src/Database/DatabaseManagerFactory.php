<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database;

use BehatDoctrineFixtures\Database\Manager\ConsoleManager\PostgreConsoleManager;
use BehatDoctrineFixtures\Database\Manager\ConsoleManager\SqliteConsoleManager;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use BehatDoctrineFixtures\Database\Exception\DatabaseManagerNotFoundForCurrentPlatform;
use BehatDoctrineFixtures\Database\Manager\DatabaseManager;
use BehatDoctrineFixtures\Database\Manager\PostgreSQLDatabaseManager;
use BehatDoctrineFixtures\Database\Manager\SqliteDatabaseManager;

class DatabaseManagerFactory
{
    private TableMetadataStorageConfiguration $migrationsStorage;
    private LoggerInterface $logger;
    private string $cacheDir;

    public function __construct(
        TableMetadataStorageConfiguration $migrationsStorage,
        LoggerInterface $logger,
        string $cacheDir
    ) {
        $this->migrationsStorage = $migrationsStorage;
        $this->logger = $logger;
        $this->cacheDir = $cacheDir;
    }

    public function createDatabaseManager(
        EntityManagerInterface $entityManager,
        string $runMigrationCommand,
        string $connectionName,
        bool $preserveMigrationsData
    ): DatabaseManager {
        $databasePlatform = $entityManager->getConnection()->getDatabasePlatform();
        $connection = $entityManager->getConnection();

        if ($databasePlatform instanceof SqlitePlatform) {
            $consoleManager = new SqliteConsoleManager();
            return new SqliteDatabaseManager(
                $consoleManager,
                $entityManager,
                $connection,
                $this->logger,
                $this->cacheDir,
                $connectionName
            );
        }

        if ($databasePlatform instanceof PostgreSQL100Platform) {
            $consoleManager = new PostgreConsoleManager($this->cacheDir, $runMigrationCommand);
            $purger = new ORMPurger($entityManager);
            $executor = new ORMExecutor($entityManager, $purger);

            return new PostgreSQLDatabaseManager(
                $consoleManager,
                $executor,
                $connection,
                $this->logger,
                $this->migrationsStorage->getTableName(),
                $this->cacheDir,
                $connectionName,
                $preserveMigrationsData
            );
        }

        throw new DatabaseManagerNotFoundForCurrentPlatform($databasePlatform);
    }
}
