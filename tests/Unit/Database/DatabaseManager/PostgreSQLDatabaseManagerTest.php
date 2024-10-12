<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Unit\Database\DatabaseManager;

use BehatDoctrineFixtures\Database\Manager\ConsoleManager\PostgreConsoleManager;
use BehatDoctrineFixtures\Database\Manager\PostgreSQLDatabaseManager;
use BehatDoctrineFixtures\Tests\Unit\Database\AbstractDatabaseManagerTest;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Result;
use Psr\Log\LoggerInterface;

final class PostgreSQLDatabaseManagerTest extends AbstractDatabaseManagerTest
{
    public function testSaveBackupSuccess()
    {
        $cacheDir = 'some/path';
        $databaseName = 'test_database';
        $connectionName = 'default';
        $dumpFilename = sprintf('%s_%s_40cd750bba9870f18aada2478b24840a.sql', $connectionName, $databaseName);
        $password = 'password';
        $user = 'user';
        $host = 'host';
        $port = 5432;
        $migrationsTable = 'migration_versions';
        $additionalParams = "--no-comments --disable-triggers --data-only -T migration_versions";

        $consoleManager = self::createMock(PostgreConsoleManager::class);
        $consoleManager->expects(self::once())
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
        $logger->expects(self::once())
            ->method('info')
            ->with(
                sprintf('Database backup saved to file %s/%s for default connection', $cacheDir, $dumpFilename),
                ['fixtures' => []]
            );

        $connection = $this->createConnectionMockWithPlatformAndParams(
            PostgreSQLPlatform::class,
            [
                'password' => $password,
                'user' => $user,
                'host' => $host,
                'port' => $port,
                'dbname' => $databaseName
            ]
        );

        $executor = self::createMock(ORMExecutor::class);

        $databaseManager = new PostgreSQLDatabaseManager(
            $consoleManager,
            $executor,
            $connection,
            $logger,
            $migrationsTable,
            $cacheDir,
            $connectionName,
            false
        );
        $databaseManager->saveBackup([]);
    }

    public function testLoadBackupSuccess()
    {
        $cacheDir = 'some/path';
        $databaseName = 'test_database';
        $connectionName = 'default';
        $dumpFilename = sprintf('%s_%s_25931488cd5177868a29c6e0328e5fc4.sql', $connectionName, $databaseName);
        $password = 'password';
        $user = 'user';
        $host = 'host';
        $port = 5432;
        $migrationsTable = 'migration_versions';

        $consoleManager = self::createMock(PostgreConsoleManager::class);
        $consoleManager->expects(self::once())
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
        $logger->expects(self::exactly(4))
            ->method('info')
            ->withConsecutive(
                ['Database created for default connection'],
                ['Migrations ran for default connection'],
                ['Schema created for default connection'],
                [
                    'Database backup loaded for default connection',
                    ['fixtures' => ['TestFixture']]
                ]
            );

        $executor = self::createMock(ORMExecutor::class);
        $executor->expects(self::exactly(1))
            ->method('purge');

        $connection = $this->createConnectionMockWithPlatformAndParams(
            PostgreSQLPlatform::class,
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

        $databaseManager = new PostgreSQLDatabaseManager(
            $consoleManager,
            $executor,
            $connection,
            $logger,
            $migrationsTable,
            $cacheDir,
            $connectionName,
            false
        );
        $databaseManager->loadBackup(['TestFixture']);
    }

    public function testPrepareSchemaWithNotCreatedSchema(): void
    {
        $cacheDir = 'some/path';
        $databaseName = 'test_database';
        $connectionName = 'default';
        $dumpFilename = sprintf('%s_%s_40cd750bba9870f18aada2478b24840a.sql', $connectionName, $databaseName);
        $password = 'password';
        $user = 'user';
        $host = 'host';
        $port = 5432;
        $migrationsTable = 'migration_versions';
        $additionalParams = "--no-comments --disable-triggers --data-only -T migration_versions";

        $consoleManager = self::createMock(PostgreConsoleManager::class);
        $consoleManager->expects(self::once())
            ->method('createDatabase');
        $consoleManager->expects(self::once())
            ->method('runMigrations');
        $consoleManager->expects(self::once())
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

        $connection = $this->createConnectionMockWithPlatformAndParams(
            PostgreSQLPlatform::class,
            [
                'password' => $password,
                'user' => $user,
                'host' => $host,
                'port' => $port,
                'dbname' => $databaseName
            ]
        );

        $executor = self::createMock(ORMExecutor::class);

        $logger = self::createMock(LoggerInterface::class);
        $logger->expects(self::exactly(4))
            ->method('info')
            ->withConsecutive(
                ['Database created for default connection'],
                ['Migrations ran for default connection'],
                ['Schema created for default connection'],
                [
                    sprintf('Database backup saved to file %s/%s for default connection', $cacheDir, $dumpFilename),
                    ['fixtures' => []]
                ]
            );

        $databaseManager = new PostgreSQLDatabaseManager(
            $consoleManager,
            $executor,
            $connection,
            $logger,
            $migrationsTable,
            $cacheDir,
            $connectionName,
            false
        );
        $databaseManager->prepareSchema();
    }

    public function testPrepareSchemaWithCreatedSchema(): void
    {
        $cacheDir = 'some/path';
        $databaseName = 'test_database';
        $connectionName = 'default';
        $dumpFilename = sprintf('%s_%s_40cd750bba9870f18aada2478b24840a.sql', $connectionName, $databaseName);
        $password = 'password';
        $user = 'user';
        $host = 'host';
        $port = 5432;
        $migrationsTable = 'migration_versions';
        $additionalParams = "--no-comments --disable-triggers --data-only -T migration_versions";

        $consoleManager = self::createMock(PostgreConsoleManager::class);
        $consoleManager->expects(self::once())
            ->method('createDatabase');
        $consoleManager->expects(self::once())
            ->method('runMigrations');
        $consoleManager->expects(self::once())
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

        $connection = $this->createConnectionMockWithPlatformAndParams(
            PostgreSQLPlatform::class,
            [
                'password' => $password,
                'user' => $user,
                'host' => $host,
                'port' => $port,
                'dbname' => $databaseName
            ]
        );

        $executor = self::createMock(ORMExecutor::class);
        $executor->expects(self::exactly(2))
            ->method('purge');

        $logger = self::createMock(LoggerInterface::class);
        $logger->expects(self::exactly(5))
            ->method('info')
            ->withConsecutive(
                ['Database created for default connection'],
                ['Migrations ran for default connection'],
                ['Schema created for default connection'],
                [
                    'Database backup saved to file some/path/default_test_database_40cd750bba9870f18aada2478b24840a.sql for default connection',
                    ['fixtures' => []]
                ],
                [
                    'Database backup loaded for default connection',
                    ['fixtures' => []]
                ]
            );

        $databaseManager = new PostgreSQLDatabaseManager(
            $consoleManager,
            $executor,
            $connection,
            $logger,
            $migrationsTable,
            $cacheDir,
            $connectionName,
            false
        );
        $databaseManager->prepareSchema();
        $databaseManager->prepareSchema();
    }
}
