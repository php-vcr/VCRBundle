<?php

namespace KPhoen\VCRBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class KPhoenVCRExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $container->setParameter('kphoen.vcr.cassette.path', $config['cassette']['path']);
        $container->setParameter('kphoen.vcr.cassette.format', $config['cassette']['format']);
        $container->setParameter('kphoen.vcr.cassette.name', $config['cassette']['name']);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();

    }

    public function getAlias()
    {
        return 'k_phoen_vcr';
    }
}
