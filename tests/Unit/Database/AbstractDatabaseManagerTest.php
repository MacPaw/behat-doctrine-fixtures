<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Unit\Database;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractDatabaseManagerTest extends TestCase
{
    /**
     * @return MockObject|Connection
     */
    protected function createConnectionMockWithPlatformAndParams(string $platformClass, array $platformParams = []) {
        $connection = self::createMock(Connection::class);
        $connection
            ->method('getDatabasePlatform')
            ->willReturn(self::createMock($platformClass));
        $connection
            ->method('getParams')
            ->willReturn($platformParams);

        return $connection;
    }
}
