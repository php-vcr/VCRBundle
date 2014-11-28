<?php

namespace VCR\VCRBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    protected $factories = array();

    public function __construct(array $factories = array())
    {
        $this->factories = $factories;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vcrvcr');

        $this->addCassetteNode($rootNode);

        return $treeBuilder;
    }

    protected function addCassetteNode(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('cassette')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('path')->defaultValue('%kernel.cache_dir%/vcr')->end()
                        ->scalarNode('format')->defaultValue('blackhole')->end()
                        ->scalarNode('name')->defaultValue('vcr')->end()
                    ->end()
                ->end()
            ->end();

        return $rootNode;
    }
}
