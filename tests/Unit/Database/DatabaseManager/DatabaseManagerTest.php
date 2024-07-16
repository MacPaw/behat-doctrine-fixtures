<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Unit\Database\DatabaseManager;

use BehatDoctrineFixtures\Database\Manager\DatabaseManager;
use PHPUnit\Framework\TestCase;

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
