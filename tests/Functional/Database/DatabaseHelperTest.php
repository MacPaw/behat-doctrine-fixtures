<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Functional\Database;

use BehatDoctrineFixtures\Database\DatabaseHelper;
use BehatDoctrineFixtures\Tests\Functional\App\Entity\BaseEntity;
use BehatDoctrineFixtures\Tests\Functional\App\TestKernel;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DatabaseHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        exec('tests/Functional/App/bin/console doctrine:database:create --no-interaction');
        exec('tests/Functional/App/bin/console d:mi:mi --no-interaction');
    }

    protected function tearDown(): void
    {
        parent::setUp();

        exec('tests/Functional/App/bin/console d:d:d --force --no-interaction');
    }

    public function testLoadFixtures(): void
    {
        $container = $this->getContainer();

        /** @var DatabaseHelper $databaseHelper */
        $databaseHelper = $container->get('behat_doctrine_fixtures.database_helper');
        $doctrine = $container->get("doctrine");

        $databaseHelper->loadFixtures([__DIR__ . '/../../Fixtures/ORM/BaseEntity.yml']);

        /** @var EntityManager $em */
        $em = $doctrine->getManager();

        $baseEntityRepository = $em->getRepository(BaseEntity::class);
        $baseEntity = $baseEntityRepository->find(1);

        self::assertInstanceOf(BaseEntity::class, $baseEntity);
        self::assertFileExists(__DIR__ . '/../App/var/cache/data.db_40cd750bba9870f18aada2478b24840a.sql');
        self::assertFileExists(__DIR__ . '/../App/var/cache/data.db_b6638dc01736134acef8b914fe820676.sql');
    }

    private function getContainer(): ContainerInterface {
        $kernel = new TestKernel();
        $kernel->boot();

        return $kernel->getContainer();
    }
}
