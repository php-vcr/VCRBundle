<?php

namespace VCR\VCRBundle\Tests\Functional;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Neutron\TemporaryFilesystem\Manager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTest;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelInterface;
use VCR\VCRBundle\DependencyInjection\VCRExtension;
use VCR\VCRBundle\Tests\Functional\App\Kernel;

class WebTestCase extends BaseWebTest
{
    use ArraySubsetAsserts;

    protected static $temporaryDirectoryManager;
    protected static $configSubFolder;

    protected static function getTemporaryDirectoryManager(): Manager
    {
        if (null === static::$temporaryDirectoryManager) {
            static::$temporaryDirectoryManager = Manager::create();
        }

        return static::$temporaryDirectoryManager;
    }

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        $class = static::getKernelClass();

        return new $class(
            $options['environment'] ?? 'test',
            $options['debug'] ?? false,
            static::getTemporaryDirectoryManager(),
            $options['config_sub_folder'] ?? static::$configSubFolder,
            $options['additional_configs'] ?? []
        );
    }

    protected function getAdditionalConfigurationLoaderCallable(
        array $config,
        string $extensionName = VCRExtension::ALIAS
    ): callable {
        return function (ContainerBuilder $container) use ($config, $extensionName) {
            $container->loadFromExtension($extensionName, $config);
        };
    }
}
