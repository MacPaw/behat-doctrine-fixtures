<?php

declare(strict_types=1);

namespace BehatDoctrineBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class BehatDoctrineExtension extends Extension
{
    /**
     * @param array<array> $configs
     *
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->loadBehatDatabaseContext($loader);
    }

    /**
     * @param array<array> $config
     */
    private function loadBehatDatabaseContext(
        XmlFileLoader $loader
    ): void {
        $loader->load('behat_database_context.xml');
    }
}
