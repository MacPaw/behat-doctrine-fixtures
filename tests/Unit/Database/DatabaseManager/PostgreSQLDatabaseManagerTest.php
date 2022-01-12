<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Unit\Database\DatabaseManager;

use BehatDoctrineFixtures\Database\Manager\ConsoleManager\PostgreConsoleManager;
use BehatDoctrineFixtures\Database\Manager\PostgreSQLDatabaseManager;
use BehatDoctrineFixtures\Tests\Unit\Database\AbstractDatabaseManagerTest;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Result;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class PostgreSQLDatabaseManagerTest extends AbstractDatabaseManagerTest
{
    public function testSaveBackupSuccess()
    {
        $cacheDir = 'some/path';
        $databaseName = 'test_database';
        $dumpFilename = sprintf('%s_40cd750bba9870f18aada2478b24840a.sql', $databaseName);
        $password = 'password';
        $user = 'user';
        $host = 'host';
        $port = 'port';
        $additionalParams = "--exclude-table=migration_versions --no-comments --disable-triggers --data-only";

        $consoleManager = self::createMock(PostgreConsoleManager::class);
        $consoleManager->expects($this->once())
            ->method('createDump')
            ->with(
                sprintf('%s/%s', $cacheDir, $dumpFilename),
                $user,
                $host,
                $port,
                $databaseName,
                $password,
                $additionalParams
            );

        $logger = self::createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with(sprintf('Database backup saved to file %s/%s', $cacheDir, $dumpFilename), ['fixtures' => []]);

        $connection = $this->createConnectionMockWithPlatformAndParams(
            PostgreSQL100Platform::class,
            [
                'password' => $password,
                'user' => $user,
                'host' => $host,
                'port' => $port,
                'dbname' => $databaseName
            ]
        );

        $executor = self::createMock(ORMExecutor::class);

        $databaseManager = new PostgreSQLDatabaseManager($consoleManager, $executor, $connection, $logger, $cacheDir);
        $databaseManager->saveBackup([]);
    }

    public function testLoadBackupSuccessWithEmptyFixtures()
    {
        $cacheDir = 'some/path';
        $databaseName = 'test_database';

        $connection = $this->createConnectionMockWithPlatformAndParams(
            PostgreSQL100Platform::class,
            [
                'dbname' => $databaseName
            ]
        );

        $executor = self::createMock(ORMExecutor::class);
        $executor->expects($this->once())
            ->method('purge');

        $consoleManager = self::createMock(PostgreConsoleManager::class);
        $logger = self::createMock(LoggerInterface::class);

        $databaseManager = new PostgreSQLDatabaseManager($consoleManager, $executor, $connection, $logger, $cacheDir);
        $databaseManager->loadBackup([]);
    }

    public function testLoadBackupSuccess()
    {
        $cacheDir = 'some/path';
        $databaseName = 'test_database';
        $dumpFilename = sprintf('%s_25931488cd5177868a29c6e0328e5fc4.sql', $databaseName);
        $password = 'password';
        $user = 'user';
        $host = 'host';
        $port = 'port';

        $consoleManager = self::createMock(PostgreConsoleManager::class);
        $consoleManager->expects($this->once())
            ->method('loadDump')
            ->with(
                sprintf('%s/%s', $cacheDir, $dumpFilename),
                $user,
                $host,
                $port,
                $databaseName,
                $password
            );

        $logger = self::createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with('Database backup loaded', []);

        $executor = self::createMock(ORMExecutor::class);
        $executor->expects($this->once())
            ->method('purge');

        $connection = $this->createConnectionMockWithPlatformAndParams(
            PostgreSQL100Platform::class,
            [
                'password' => $password,
                'user' => $user,
                'host' => $host,
                'port' => $port,
                'dbname' => $databaseName
            ]
        );

        $queryResult = self::createMock(Result::class);
        $queryResult
            ->method('fetchAllAssociative')
            ->willReturn([]);
        $connection
            ->method('executeQuery')
            ->willReturn($queryResult);

        $databaseManager = new PostgreSQLDatabaseManager($consoleManager, $executor, $connection, $logger, $cacheDir);
        $databaseManager->loadBackup(['TestFixture']);
    }

    public function testPrepareSchemaWithNotCreatedSchema(): void
    {
        $cacheDir = 'some/path';
        $databaseName = 'test_database';

        $connection = $this->createConnectionMockWithPlatformAndParams(
            PostgreSQL100Platform::class,
            [
                'dbname' => $databaseName
            ]
        );

        $executor = self::createMock(ORMExecutor::class);
        $executor->expects($this->once())
            ->method('purge');

        $consoleManager = self::createMock(PostgreConsoleManager::class);
        $consoleManager->expects(self::once())
            ->method('createDatabase');
        $consoleManager->expects(self::once())
            ->method('runMigrations');

        $logger = self::createMock(LoggerInterface::class);
        $logger->expects(self::exactly(2))
            ->method('info')
            ->withConsecutive(
                ['Database created'],
                ['Schema created']
            );

        $databaseManager = new PostgreSQLDatabaseManager($consoleManager, $executor, $connection, $logger, $cacheDir);
        $databaseManager->prepareSchema();
    }

    public function testPrepareSchemaWithCreatedSchema(): void
    {
        $cacheDir = 'some/path';
        $databaseName = 'test_database';

        $connection = $this->createConnectionMockWithPlatformAndParams(
            PostgreSQL100Platform::class,
            [
                'dbname' => $databaseName
            ]
        );

        $executor = self::createMock(ORMExecutor::class);
        $executor->expects($this->exactly(2))
            ->method('purge');

        $consoleManager = self::createMock(PostgreConsoleManager::class);
        $consoleManager->expects(self::once())
            ->method('createDatabase');
        $consoleManager->expects(self::once())
            ->method('runMigrations');

        $logger = self::createMock(LoggerInterface::class);
        $logger->expects(self::exactly(2))
            ->method('info')
            ->withConsecutive(
                ['Database created'],
                ['Schema created']
            );

        $databaseManager = new PostgreSQLDatabaseManager($consoleManager, $executor, $connection, $logger, $cacheDir);
        $databaseManager->prepareSchema();
        $databaseManager->prepareSchema();
    }
}
