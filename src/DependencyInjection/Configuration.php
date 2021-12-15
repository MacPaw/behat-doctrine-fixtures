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

        return $treeBuilder;
    }

    private function addDatabaseContextSection(NodeBuilder $builder): void
    {
        $builder
            ->arrayNode('database_context')
                ->children()
                    ->scalarNode('dataFixturesPath')->cannotBeEmpty()->end()
                ->end()
                ->canBeEnabled()
            ->end()
        ;
    }
}
