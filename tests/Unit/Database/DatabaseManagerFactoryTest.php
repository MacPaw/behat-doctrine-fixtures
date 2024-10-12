<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Unit\Database;

use BehatDoctrineFixtures\Database\DatabaseManagerFactory;
use BehatDoctrineFixtures\Database\Exception\DatabaseManagerNotFoundForCurrentPlatform;
use BehatDoctrineFixtures\Database\Manager\PostgreSQLDatabaseManager;
use BehatDoctrineFixtures\Database\Manager\SqliteDatabaseManager;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

final class DatabaseManagerFactoryTest extends AbstractDatabaseManagerTest
{
    private ?DatabaseManagerFactory $databaseManagerFactory = null;

    protected function setUp(): void
    {
        parent::setUp();

        $migrationStorageConfiguration = new TableMetadataStorageConfiguration();
        $logger = self::createMock(LoggerInterface::class);
        $cacheDir = 'test/path';

        $this->databaseManagerFactory = new DatabaseManagerFactory($migrationStorageConfiguration, $logger, $cacheDir);
    }

    public function testCreatePostgreSQLDatabaseManagerSuccess(): void
    {
        $entityManager = $this->createEntityManagerMockWithPlatform(PostgreSQLPlatform::class);

        $eventManager = self::createMock(EventManager::class);
        $eventManager->expects(self::once())
            ->method('addEventSubscriber');

        $entityManager->expects(self::once())
            ->method('getEventManager')
            ->willReturn($eventManager);

        $databaseManager = $this->databaseManagerFactory->createDatabaseManager($entityManager,'', '', false);

        self::assertInstanceOf(PostgreSQLDatabaseManager::class, $databaseManager);
    }

    public function testCreateSqliteDatabaseManagerSuccess(): void
    {
        $entityManager = $this->createEntityManagerMockWithPlatform(SqlitePlatform::class);
        $databaseManager = $this->databaseManagerFactory->createDatabaseManager($entityManager,'', '', false);

        self::assertInstanceOf(SqliteDatabaseManager::class, $databaseManager);
    }

    public function testCreateDatabaseManagerFail(): void
    {
        $entityManager = $this->createEntityManagerMockWithPlatform(MySQLPlatform::class);

        $this->expectException(DatabaseManagerNotFoundForCurrentPlatform::class);
        $this->databaseManagerFactory->createDatabaseManager($entityManager,'', '', false);
    }

    /**
     * @return MockObject|EntityManagerInterface
     */
    private function createEntityManagerMockWithPlatform(string $platformClass)
    {
        $connection = $this->createConnectionMockWithPlatformAndParams($platformClass);

        $entityManager = self::createMock(EntityManagerInterface::class);
        $entityManager
            ->method('getConnection')
            ->willReturn($connection);

        return $entityManager;
    }
}
