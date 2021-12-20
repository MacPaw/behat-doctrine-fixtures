<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database;

use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use BehatDoctrineFixtures\Database\Exception\DatabaseManagerNotFoundForCurrentPlatform;
use BehatDoctrineFixtures\Database\Manager\DatabaseManager;
use BehatDoctrineFixtures\Database\Manager\PostgreSQLDatabaseManager;
use BehatDoctrineFixtures\Database\Manager\SqliteDatabaseManager;

class DatabaseManagerFactory
{
    public static function createDatabaseManager(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        string $cacheDir
    ): DatabaseManager {
        $databasePlatform = $entityManager->getConnection()->getDatabasePlatform();

        if ($databasePlatform instanceof SqlitePlatform) {
            return new SqliteDatabaseManager($entityManager, $logger, $cacheDir);
        }

        if ($databasePlatform instanceof PostgreSQL100Platform) {
            return new PostgreSQLDatabaseManager($entityManager, $logger, $cacheDir);
        }

        throw new DatabaseManagerNotFoundForCurrentPlatform($databasePlatform);
    }
}
