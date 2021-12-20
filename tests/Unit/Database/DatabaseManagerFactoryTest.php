<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Unit\DatabaseManager;

use BehatDoctrineFixtures\Database\DatabaseManagerFactory;
use BehatDoctrineFixtures\Database\Exception\DatabaseManagerNotFoundForCurrentPlatform;
use BehatDoctrineFixtures\Database\Manager\PostgreSQLDatabaseManager;
use BehatDoctrineFixtures\Database\Manager\SqliteDatabaseManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class DatabaseManagerFactoryTest extends TestCase
{
    /**
     * @param array $expectedMonologHandlerDecoratorConfiguration
     *
     * @dataProvider getDatabaseManagerOptionsProvider
     */
    public function testCreateDatabaseManagerSuccess(
        EntityManagerInterface $entityManager,
        string $expectedDatabaseManagerClassName
    ): void {
        $logger = self::createMock(LoggerInterface::class);
        $cacheDir = 'test/path';

        $databaseManager = DatabaseManagerFactory::createDatabaseManager(
            $entityManager,
            $logger,
            $cacheDir
        );

        self::assertInstanceOf($expectedDatabaseManagerClassName, $databaseManager);
    }

    public function getDatabaseManagerOptionsProvider(): array
    {
        return [
            [
                $this->createEntityManagerMockWithPlatform(SqlitePlatform::class),
                SqliteDatabaseManager::class
            ],
            [
                $this->createEntityManagerMockWithPlatform(PostgreSQL100Platform::class),
                PostgreSQLDatabaseManager::class
            ]
        ];
    }

    public function testCreateDatabaseManagerFail(): void
    {
        $entityManager = $this->createEntityManagerMockWithPlatform(MySQLPlatform::class);
        $logger = self::createMock(LoggerInterface::class);
        $cacheDir = 'test/path';

        $this->expectException(DatabaseManagerNotFoundForCurrentPlatform::class);
        DatabaseManagerFactory::createDatabaseManager(
            $entityManager,
            $logger,
            $cacheDir
        );
    }

    private function createEntityManagerMockWithPlatform(string $platformClass): EntityManagerInterface
    {
        $connection = self::createMock(Connection::class);
        $connection
            ->method('getDatabasePlatform')
            ->willReturn(self::createMock($platformClass));

        $entityManager = self::createMock(EntityManagerInterface::class);
        $entityManager
            ->method('getConnection')
            ->willReturn($connection);

        return $entityManager;
    }
}
