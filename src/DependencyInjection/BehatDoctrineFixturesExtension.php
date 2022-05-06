<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\DependencyInjection;

use BehatDoctrineFixtures\Database\DatabaseHelper;
use BehatDoctrineFixtures\Database\DatabaseManagerFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

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

        $this->loadDatabaseHelpers($config, $loader, $container);
        $this->loadBehatDatabaseContext($config, $loader);
    }

    /**
     * @param array<array> $config
     */
    private function loadBehatDatabaseContext(
        array $config,
        XmlFileLoader $loader
    ): void {
        $databaseContextConfig = $config['database_context'];

        if ($databaseContextConfig) {
            $loader->load('database_context.xml');
        }
    }

    private function loadDatabaseHelpers(
        array $config,
        XmlFileLoader $loader,
        ContainerBuilder $container
    ): void {
        $loader->load('database_manager_factory.xml');

        $databaseManagerFactoryDefinition = $container->findDefinition(DatabaseManagerFactory::class);
        $entityManagers = $container->getParameter('doctrine.entity_managers');
        foreach ($config['connections'] as $connectionName => $connectionParams) {
            $databaseHelperDefinition = new Definition(DatabaseHelper::class);
            $databaseHelperDefinition->addTag('behat_doctrine_fixtures.database_helper');
            $databaseHelperDefinition
                ->setArguments([
                    $databaseManagerFactoryDefinition,
                    new Reference($entityManagers[$connectionName]),
                    new Reference('fidry_alice_data_fixtures.doctrine.persister_loader'),
                    $connectionParams['database_fixtures_paths'],
                    $connectionParams['run_migrations_command'],
                    $connectionName,
                    $connectionParams['preserve_migrations_data']
                ])
                ->setPublic(true);

            $container->setDefinition(
                sprintf('behat_doctrine_fixtures.database_helper.%s', $connectionName),
                $databaseHelperDefinition
            );
        }
    }
}
