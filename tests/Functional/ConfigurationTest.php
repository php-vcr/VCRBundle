<?php

namespace VCR\VCRBundle\Tests\Functional;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use VCR\VCRBundle\DependencyInjection\VCRVCRExtension;

class ConfigurationTest extends WebTestCase
{
    /**
     * @param string $name
     * @param array $kernelOptions
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    protected function getContainerConfiguration(string $name, array $kernelOptions = []): array
    {
        static::bootKernel($kernelOptions);
        $kernel = static::$kernel;

        $method = new \ReflectionMethod($kernel, 'buildContainer');
        $method->setAccessible(true);
        /** @var ContainerBuilder $containerBuilder */
        $containerBuilder = $method->invoke($kernel);
        $containerBuilder->getCompiler()->compile($containerBuilder);

        $extension = null;
        foreach ($kernel->getBundles() as $bundle) {
            if ($bundle->getContainerExtension() instanceof ExtensionInterface
                && ($name === $bundle->getName() || $name === $bundle->getContainerExtension()->getAlias())
            ) {
                $extension = $bundle->getContainerExtension();
                break;
            }
        }

        static::assertInstanceOf(
            ExtensionInterface::class,
            $extension,
            sprintf('Container extension "%s" not loaded or found.', $name)
        );
        /** @var $extension ExtensionInterface */

        $configs = $containerBuilder->getExtensionConfig($extension->getAlias());
        $configuration = $extension->getConfiguration($configs, $containerBuilder);

        $configs = $containerBuilder->getParameterBag()->resolveValue($configs);
        if (method_exists($containerBuilder, 'resolveEnvPlaceholders')) {
            $configs = $containerBuilder->resolveEnvPlaceholders($configs);
        }

        $processor = new Processor();
        $processedConfiguration = $processor->processConfiguration($configuration, $configs);
        $processedConfiguration = $containerBuilder->getParameterBag()->resolveValue($processedConfiguration);
        if (method_exists($containerBuilder, 'resolveEnvPlaceholders')) {
            $processedConfiguration = $containerBuilder->resolveEnvPlaceholders($processedConfiguration);
        }

        static::assertIsArray($processedConfiguration, 'Invalid extension configuration.');

        return $processedConfiguration;
    }

    /**
     * @param array $configuration
     *
     * @return array
     */
    protected function mergeWithDefaultConfiguration(array $configuration = []): array
    {
        $configuration = array_replace_recursive(
            [
                'enabled' => true,
                'library_hooks' => [
                    'stream_wrapper' => false,
                    'curl' => false,
                    'soap' => false,
                ],
                'request_matchers' => [
                    'method' => true,
                    'url' => true,
                    'query_string' => true,
                    'host' => true,
                    'headers' => true,
                    'body' => true,
                    'post_fields' => true,
                ],
                'cassette' => [
                    'name' => 'vcr',
                    'type' => 'blackhole',
                ],
            ],
            $configuration
        );

        return $configuration;
    }

    /**
     * @dataProvider configurationDataProvider
     *
     * @param array $expectedConfig
     * @param array $additionalConfig
     *
     * @return void
     */
    public function testConfiguration(array $expectedConfig, array $additionalConfig = []): void
    {
        $kernelOptions = [
            'additional_configs' => [
                $this->getAdditionalConfigurationLoaderCallable($additionalConfig),
            ],
        ];
        $config = $this->getContainerConfiguration(VCRVCRExtension::ALIAS, $kernelOptions);

        static::assertArraySubset($expectedConfig, $config);
    }

    /**
     * @return array
     */
    public function configurationDataProvider(): array
    {
        return [
            'Default configuration' => [
                $this->mergeWithDefaultConfiguration(),
            ],
        ];
    }
}
