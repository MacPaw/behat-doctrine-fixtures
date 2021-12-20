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
            'database_context' => [
                'enabled' => false
            ]
        ];

        $this->assertSame($expectedBundleDefaultConfig, $this->processConfiguration([]));
    }

    /**
     * @param array|bool $monologHandlerDecoratorConfiguration
     * @param array $expectedMonologHandlerDecoratorConfiguration
     *
     * @dataProvider getDatabaseContextOptionsProvider
     */
    public function testDatabaseContextOptions(
        $databaseContextConfiguration,
        array $expectedDatabaseContextConfiguration
    ): void {
        $config = $this->processConfiguration(['database_context' => $databaseContextConfiguration]);

        $this->assertSame($expectedDatabaseContextConfiguration, $config['database_context']);
    }

    public function getDatabaseContextOptionsProvider(): array
    {
        return [
            [
                [
                    'dataFixturesPath' => 'some/path'
                ],
                [
                    'dataFixturesPath' => 'some/path',
                    'enabled' => true
                ]
            ],
            [
                false,
                [
                    'enabled' => false
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
