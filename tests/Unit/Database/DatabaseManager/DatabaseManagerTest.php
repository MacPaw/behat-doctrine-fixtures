<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Unit\Database\DatabaseManager;

use BehatDoctrineFixtures\Database\DatabaseManagerFactory;
use BehatDoctrineFixtures\Database\Exception\DatabaseManagerNotFoundForCurrentPlatform;
use BehatDoctrineFixtures\Database\Manager\DatabaseManager;
use BehatDoctrineFixtures\Database\Manager\PostgreSQLDatabaseManager;
use BehatDoctrineFixtures\Database\Manager\SqliteDatabaseManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionClass;

final class DatabaseManagerTest extends TestCase
{
    public function testBackupExistsSuccess()
    {
        $databaseManager = $this->getMockBuilder(DatabaseManager::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $databaseManager->expects($this->once())
            ->method('getBackupFilename')
            ->willReturn(__DIR__ . '/../../../Fixtures/Database/test_dump.sql');

        self::assertTrue($databaseManager->backupExists([]));
    }
}
