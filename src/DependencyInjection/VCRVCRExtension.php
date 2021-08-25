<?php

namespace VCR\VCRBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class VCRVCRExtension extends Extension
{
    const ALIAS = 'vcrvcr';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('listeners.yml');
        $loader->load('services.yml');

        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $enabled_library_hooks = array_keys(array_filter($config['library_hooks']));
        $container->setParameter('vcr.library_hooks', $enabled_library_hooks);

        $enabled_request_matchers = array_keys(array_filter($config['request_matchers']));
        $container->setParameter('vcr.request_matchers', $enabled_request_matchers);

        $container->setParameter('vcr.enabled', $config['enabled']);
        $container->setParameter('vcr.cassette.path', $config['cassette']['path']);
        $container->setParameter('vcr.cassette.type', $config['cassette']['type']);
        $container->setParameter('vcr.cassette.name', $config['cassette']['name']);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        return new Configuration();
    }

    public function getAlias(): string
    {
        return static::ALIAS;
    }
}
