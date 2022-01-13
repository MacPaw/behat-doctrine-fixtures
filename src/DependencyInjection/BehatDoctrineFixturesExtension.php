<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\DependencyInjection;

use BehatDoctrineFixtures\Context\DatabaseContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class BehatDoctrineFixturesExtension extends Extension
{
    /**
     * @param array<array> $configs
     *
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->loadDatabaseHelper($loader);
        $this->loadBehatDatabaseContext($config, $loader, $container);
    }

    /**
     * @param array<array> $config
     */
    private function loadBehatDatabaseContext(
        array $config,
        XmlFileLoader $loader,
        ContainerBuilder $container
    ): void {
        $databaseContextConfig = $config['database_context'];

        if ($databaseContextConfig['enabled']) {
            $loader->load('database_context.xml');

            $databaseContextDefinition = $container->findDefinition(DatabaseContext::class);
            $databaseContextDefinition->setArgument('$dataFixturesPath', $databaseContextConfig['dataFixturesPath']);
        }
    }

    private function loadDatabaseHelper(
        XmlFileLoader $loader
    ): void {
        $loader->load('database_helper.xml');
    }
}
