<?php

declare(strict_types=1);

namespace BehatDoctrineBundle\Database;

use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use BehatDoctrineBundle\Database\Exception\DatabaseManagerNotFoundForCurrentPlatform;
use BehatDoctrineBundle\Database\Manager\DatabaseManager;
use BehatDoctrineBundle\Database\Manager\PgSqlDatabaseManager;
use BehatDoctrineBundle\Database\Manager\SQLiteDatabaseManager;

class DatabaseManagerFactory
{
    static function createDatabaseManager(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        string $cacheDir
    ): DatabaseManager {
        $databasePlatform = $entityManager->getConnection()->getDatabasePlatform();

        if($databasePlatform instanceof SqlitePlatform){
            return new SQLiteDatabaseManager($entityManager, $logger, $cacheDir);
        }

        if($databasePlatform instanceof PostgreSQL100Platform){
            return new PgSqlDatabaseManager($entityManager, $logger, $cacheDir);
        }

        throw new DatabaseManagerNotFoundForCurrentPlatform($databasePlatform);
    }
}
