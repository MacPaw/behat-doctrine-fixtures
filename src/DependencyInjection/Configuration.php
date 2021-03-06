<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('behat_doctrine_fixtures');
        $root = $treeBuilder->getRootNode()->children();

        $this->addDatabaseContextSection($root);
        $this->addConnectionsSection($root);

        return $treeBuilder;
    }

    private function addDatabaseContextSection(NodeBuilder $builder): void
    {
        $builder->booleanNode('database_context')->defaultTrue()->end();
    }

    private function addConnectionsSection(NodeBuilder $builder): void
    {
        $builder->arrayNode('connections')
            ->prototype('array')
                ->children()
                    ->scalarNode('run_migrations_command')
                        ->defaultValue('bin/console d:m:m --env=test --no-interaction --allow-no-migration')->end()
                    ->booleanNode('preserve_migrations_data')
                        ->defaultValue(false)->end()
                    ->arrayNode('database_fixtures_paths')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
