<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Unit\Context;

use BehatDoctrineFixtures\Context\DatabaseContext;
use BehatDoctrineFixtures\Database\DatabaseHelper;
use BehatDoctrineFixtures\Database\DatabaseHelperCollection;
use PHPUnit\Framework\TestCase;

class DatabaseContextTest extends TestCase
{
    public function testLoadFixturesForDefaultConnection(): void
    {
        $fixtureAliases = 'Base';
        $expectedFixtureAliases = ['Base'];

        $databaseHelper = self::createMock(DatabaseHelper::class);
        $databaseHelper->expects(self::once())
            ->method('getConnectionName')
            ->willReturn('default');
        $databaseHelper->expects(self::once())
            ->method('loadFixtures')
            ->with($expectedFixtureAliases);

        $databaseContext = $this->createDatabaseContextWithHelper($databaseHelper);
        $databaseContext->loadFixturesForDefaultConnection($fixtureAliases);
    }

    public function testLoadFixturesForGivenConnectionSuccess(): void
    {
        $connectionName = 'custom';
        $fixtureAliases = 'Base';
        $expectedFixtureAliases = ['Base'];

        $databaseHelper = self::createMock(DatabaseHelper::class);
        $databaseHelper->expects(self::once())
            ->method('getConnectionName')
            ->willReturn($connectionName);
        $databaseHelper->expects(self::once())
            ->method('loadFixtures')
            ->with($expectedFixtureAliases);

        $databaseContext = $this->createDatabaseContextWithHelper($databaseHelper);
        $databaseContext->loadFixturesForGivenConnection($fixtureAliases, $connectionName);
    }

    private function createDatabaseContextWithHelper(DatabaseHelper $databaseHelper): DatabaseContext
    {
        $helperCollection = new DatabaseHelperCollection([$databaseHelper]);

        return new DatabaseContext($helperCollection);
    }
}
