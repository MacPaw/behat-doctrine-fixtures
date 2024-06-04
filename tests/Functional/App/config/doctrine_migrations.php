<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine_migrations', [
        'storage' => [
            'table_storage' => [
                'table_name' => 'migration_versions',
            ],
        ],
        'migrations_paths' => [
            'BehatDoctrineFixtures\Tests\Functional\App\Migrations' => '%kernel.project_dir%/Migrations',
        ],
        'organize_migrations' => 'BY_YEAR',
        'custom_template' => '%kernel.project_dir%/config/doctrine_migrations.template',
    ]);
};
