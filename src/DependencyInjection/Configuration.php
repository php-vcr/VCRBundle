<?php
declare(strict_types = 1);

namespace VCR\VCRBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(VCRVCRExtension::ALIAS);
        $rootNode = $treeBuilder->getRootNode();

        $this->addCassetteNode($rootNode);

        return $treeBuilder;
    }

    protected function addCassetteNode(ArrayNodeDefinition $rootNode): ArrayNodeDefinition
    {
        $rootNode
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->arrayNode('library_hooks')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('stream_wrapper')->defaultValue(false)->end()
                        ->booleanNode('curl')->defaultValue(false)->end()
                        ->booleanNode('soap')->defaultValue(false)->end()
                    ->end()
                ->end()
                ->arrayNode('request_matchers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('method')->defaultValue(true)->end()
                        ->booleanNode('url')->defaultValue(true)->end()
                        ->booleanNode('query_string')->defaultValue(true)->end()
                        ->booleanNode('host')->defaultValue(true)->end()
                        ->booleanNode('headers')->defaultValue(true)->end()
                        ->booleanNode('body')->defaultValue(true)->end()
                        ->booleanNode('post_fields')->defaultValue(true)->end()
                    ->end()
                ->end()
                ->arrayNode('cassette')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('path')->defaultValue('%kernel.cache_dir%/vcr')->end()
                        ->scalarNode('type')->defaultValue('blackhole')->end()
                        ->scalarNode('name')->defaultValue('vcr')->end()
                    ->end()
                ->end()
            ->end();

        return $rootNode;
    }
}
