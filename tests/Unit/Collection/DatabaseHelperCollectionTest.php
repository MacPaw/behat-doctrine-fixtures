<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Unit\Collection;

use BehatDoctrineFixtures\Database\DatabaseHelper;
use BehatDoctrineFixtures\Database\DatabaseHelperCollection;
use BehatDoctrineFixtures\Database\Exception\AlreadyExistDatabaseHelperCollectionException;
use BehatDoctrineFixtures\Database\Exception\NotFoundDatabaseHelperCollectionException;
use PHPUnit\Framework\TestCase;
use TypeError;

class DatabaseHelperCollectionTest extends TestCase
{
    public function testInitFailed(): void
    {
        $this->expectException(TypeError::class);
        new DatabaseHelperCollection(['string']);
    }

    public function testAddDatabaseHelperSuccess(): void
    {
        $databaseHelper = self::createMock(DatabaseHelper::class);
        $databaseHelper->expects(self::once())
            ->method('getConnectionName')
            ->willReturn('default');

        $helperCollection = new DatabaseHelperCollection([]);
        $helperCollection->add($databaseHelper);

        $this->assertInstanceOf(DatabaseHelper::class, $helperCollection->getDatabaseHelperByConnectionName('default'));
    }

    public function testAddDatabaseHelperFailed(): void
    {
        $databaseHelper = self::createMock(DatabaseHelper::class);
        $databaseHelper->expects(self::exactly(2))
            ->method('getConnectionName')
            ->willReturn('default');

        $helperCollection = new DatabaseHelperCollection([$databaseHelper]);

        $this->expectException(AlreadyExistDatabaseHelperCollectionException::class);
        $helperCollection->add($databaseHelper);
    }

    public function testGetDatabaseHelperFailed(): void
    {
        $helperCollection = new DatabaseHelperCollection([]);

        $this->expectException(NotFoundDatabaseHelperCollectionException::class);
        $helperCollection->getDatabaseHelperByConnectionName('default');
    }
}
