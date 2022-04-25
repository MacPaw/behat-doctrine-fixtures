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
                        ]
                    ]
                ],
                [
                    'connections' => [
                        'default' => [
                            'databaseFixturesPaths' => ['test/path'],
                            'runMigrationsCommand' =>
                                'bin/console d:m:m --env=test --no-interaction --allow-no-migration',
                            'preserveMigrationsData' => false
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
                            'runMigrationsCommand' => 'some command',
                            'preserveMigrationsData' => true
                        ],
                        'test' => [
                            'databaseFixturesPaths' => ['test/path'],
                            'runMigrationsCommand' => 'test command',
                            'preserveMigrationsData' => true
                        ]
                    ]
                ],
                [
                    'database_context' => false,
                    'connections' => [
                        'default' => [
                            'databaseFixturesPaths' => ['test/path'],
                            'runMigrationsCommand' => 'some command',
                            'preserveMigrationsData' => true
                        ],
                        'test' => [
                            'databaseFixturesPaths' => ['test/path'],
                            'runMigrationsCommand' => 'test command',
                            'preserveMigrationsData' => true
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
