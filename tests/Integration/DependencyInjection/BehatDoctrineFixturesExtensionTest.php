<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Integration\DependencyInjection;

use BehatDoctrineFixtures\Context\DatabaseContext;
use BehatDoctrineFixtures\Database\DatabaseHelper;
use BehatDoctrineFixtures\Database\DatabaseHelperCollection;
use BehatDoctrineFixtures\Database\DatabaseManagerFactory;
use BehatDoctrineFixtures\DependencyInjection\BehatDoctrineFixturesExtension;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BehatDoctrineFixturesExtensionTest extends TestCase
{
    public function testWithEmptyConfig(): void
    {
        $container = $this->createContainerFromFixture('min_bundle_config');

        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage(
            'You have requested a non-existent service "BehatDoctrineFixtures\Context\DatabaseContext".'
        );

        $container->getDefinition(DatabaseContext::class);
    }

    public function testWithFullConfig(): void
    {
        $container = $this->createContainerFromFixture('full_bundle_config');

        $databaseHelperDefinition = $container->getDefinition('behat_doctrine_fixtures.database_helper.default');
        self::assertSame(DatabaseHelper::class, $databaseHelperDefinition->getClass());

        self::assertInstanceOf(Definition::class, $databaseHelperDefinition->getArgument(0));
        self::assertSame(DatabaseManagerFactory::class, $databaseHelperDefinition->getArgument(0)->getClass());

        self::assertSame('doctrine.orm.default_entity_manager', (string) $databaseHelperDefinition->getArgument(1));
        self::assertSame('fidry_alice_data_fixtures.doctrine.persister_loader', (string) $databaseHelperDefinition->getArgument(2));

        self::assertSame(['../../../Fixtures'], $databaseHelperDefinition->getArgument(3));
        self::assertSame(['doctrine_migrations'], $databaseHelperDefinition->getArgument(4));

        $databaseManagerFactoryDefinition = $container->getDefinition(DatabaseManagerFactory::class);
        self::assertSame(DatabaseManagerFactory::class, $databaseManagerFactoryDefinition->getClass());
        self::assertSame('%kernel.cache_dir%',
            (string) $databaseManagerFactoryDefinition->getArgument('$cacheDir')
        );

        $databaseContextDefinition = $container->getDefinition(DatabaseContext::class);
        self::assertSame(DatabaseContext::class, $databaseContextDefinition->getClass());
        self::assertSame(
            DatabaseHelperCollection::class,
            (string) $databaseContextDefinition->getArgument('$databaseHelperCollection')
        );
    }

    private function createContainerFromFixture(string $fixtureFile): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $container->registerExtension(new BehatDoctrineFixturesExtension());
        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        $this->loadFixture($container, $fixtureFile);

        $container->setParameter('doctrine.entity_managers', [
            "default" => "doctrine.orm.default_entity_manager",
        ]);
        $container->compile();

        return $container;
    }

    protected function loadFixture(ContainerBuilder $container, string $fixtureFile): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Fixtures/Configuration'));
        $loader->load($fixtureFile . '.yaml');
    }
}
