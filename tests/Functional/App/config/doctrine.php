<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => 'sqlite:///%kernel.cache_dir%/data.db',
            'options' => [
                'collation' => 'utf8mb4_unicode_ci',
            ],
        ],
        'orm' => [
            'auto_generate_proxy_classes' => true,
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'auto_mapping' => true,
            'mappings' => [
                'App' => [
                    'is_bundle' => false,
                    'type' => 'xml',
                    'dir' => '%kernel.project_dir%/config/doctrine_entity',
                    'prefix' => 'BehatDoctrineFixtures\Tests\Functional\App\Entity',
                    'alias' => 'App',
                ],
            ],
        ],
    ]);
};
