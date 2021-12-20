<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Integration\DependencyInjection;

use BehatDoctrineFixtures\Context\DatabaseContext;
use BehatDoctrineFixtures\Database\DatabaseHelper;
use BehatDoctrineFixtures\DependencyInjection\BehatDoctrineFixturesExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BehatDoctrineFixturesExtensionTest extends TestCase
{
    public function testWithEmptyConfig(): void
    {
        $container = $this->createContainerFromFixture('empty_bundle_config');

        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage(
            'You have requested a non-existent service "behat_doctrine_fixtures.database_context".'
        );

        $container->getDefinition('behat_doctrine_fixtures.database_context');
    }

    public function testWithFullConfig(): void
    {
        $container = $this->createContainerFromFixture('database_context_bundle_config');

        $databaseHelperContext = $container->getDefinition('behat_doctrine_fixtures.database_helper');
        self::assertSame(DatabaseHelper::class, $databaseHelperContext->getClass());
        self::assertSame(
            '%kernel.cache_dir%',
            (string) $databaseHelperContext->getArgument('$cacheDir')
        );
        self::assertSame(
            'fidry_alice_data_fixtures.doctrine.persister_loader',
            (string) $databaseHelperContext->getArgument('$fixturesLoader')
        );

        $databaseContextDefinition = $container->getDefinition('behat_doctrine_fixtures.database_context');
        self::assertSame(DatabaseContext::class, $databaseContextDefinition->getClass());
        self::assertSame(
            'behat_doctrine_fixtures.database_helper',
            (string) $databaseContextDefinition->getArgument('$databaseHelper')
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

        $container->compile();

        return $container;
    }

    protected function loadFixture(ContainerBuilder $container, string $fixtureFile): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Fixtures/Configuration'));
        $loader->load($fixtureFile . '.yaml');
    }
}
