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
                            'databaseFixturesPaths' => ['test/path'],
                            'excludedTables' => ['migrations'],
                            'runMigrationsCommand' => 'some command'
                        ]
                    ]
                ],
                [
                    'connections' => [
                        'default' => [
                            'databaseFixturesPaths' => ['test/path'],
                            'excludedTables' => ['migrations'],
                            'runMigrationsCommand' => 'some command'
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
                            'databaseFixturesPaths' => ['test/path'],
                            'excludedTables' => ['migrations'],
                            'runMigrationsCommand' => 'some command'
                        ],
                        'test' => [
                            'databaseFixturesPaths' => ['test/path'],
                            'excludedTables' => ['test_migrations'],
                            'runMigrationsCommand' => 'test command'
                        ]
                    ]
                ],
                [
                    'database_context' => false,
                    'connections' => [
                        'default' => [
                            'databaseFixturesPaths' => ['test/path'],
                            'excludedTables' => ['migrations'],
                            'runMigrationsCommand' => 'some command'
                        ],
                        'test' => [
                            'databaseFixturesPaths' => ['test/path'],
                            'excludedTables' => ['test_migrations'],
                            'runMigrationsCommand' => 'test command'
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
