<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Integration\DependencyInjection;

use PHPUnit\Framework\TestCase;
use BehatDoctrineFixtures\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testProcessConfigurationWithDefaultConfiguration(): void
    {
        $expectedBundleDefaultConfig = [
            'database_context' => true,
            'connections' => []
        ];

        $this->assertSame($expectedBundleDefaultConfig, $this->processConfiguration([]));
    }

    /**
     * @param array|bool $databaseContextConfiguration
     * @param array $expectedDatabaseContextConfiguration
     *
     * @dataProvider getDatabaseContextOptionsProvider
     */
    public function testDatabaseHelperOptions(
        $databaseContextConfiguration,
        array $expectedDatabaseContextConfiguration
    ): void {
        $config = $this->processConfiguration($databaseContextConfiguration);

        $this->assertSame($expectedDatabaseContextConfiguration, $config);
    }

    public function getDatabaseContextOptionsProvider(): array
    {
        return [
            [
                [
                    'connections' => [
                        'default' => [
                            'database_fixtures_paths' => ['test/path'],
                        ]
                    ]
                ],
                [
                    'connections' => [
                        'default' => [
                            'database_fixtures_paths' => ['test/path'],
                            'run_migrations_command' =>
                                'bin/console d:m:m --env=test --no-interaction --allow-no-migration',
                            'preserve_migrations_data' => false
                        ]
                    ],
                    'database_context' => true
                ]
            ],
            [
                [
                    'database_context' => false,
                    'connections' => [
                        'default' => [
                            'database_fixtures_paths' => ['test/path'],
                            'run_migrations_command' => 'some command',
                            'preserve_migrations_data' => true
                        ],
                        'test' => [
                            'database_fixtures_paths' => ['test/path'],
                            'run_migrations_command' => 'test command',
                            'preserve_migrations_data' => true
                        ]
                    ]
                ],
                [
                    'database_context' => false,
                    'connections' => [
                        'default' => [
                            'database_fixtures_paths' => ['test/path'],
                            'run_migrations_command' => 'some command',
                            'preserve_migrations_data' => true
                        ],
                        'test' => [
                            'database_fixtures_paths' => ['test/path'],
                            'run_migrations_command' => 'test command',
                            'preserve_migrations_data' => true
                        ]
                    ]
                ]
            ]
        ];
    }

    private function processConfiguration(array $values): array
    {
        $processor = new Processor();

        return $processor->processConfiguration(new Configuration(), ['behat_doctrine_fixtures' => $values]);
    }
}
