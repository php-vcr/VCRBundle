<?php

namespace VCR\VCRBundle\Tests\Functional\App;

use Neutron\TemporaryFilesystem\Manager;
use Neutron\TemporaryFilesystem\TemporaryFilesystemInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use VCR\VCRBundle\VCRVCRBundle;

class Kernel extends BaseKernel
{
    protected $temporaryFilesystem;

    protected $temporaryDirectory;

    protected $configFolder;

    protected $additionalConfigs;

    public function __construct(
        string $environment,
        bool $debug,
        ?TemporaryFilesystemInterface $tempFs = null,
        ?string $configSubFolder = null,
        array $additionalConfigs = []
    ) {
        parent::__construct($environment, $debug);
        $this->temporaryFilesystem = $tempFs ?? Manager::create();
        $basePath = realpath(__DIR__ . '/config/');
        $configSubFolder = $configSubFolder ?? 'Default';
        $this->configFolder = $basePath . DIRECTORY_SEPARATOR . trim($configSubFolder, DIRECTORY_SEPARATOR);
        $this->additionalConfigs = $additionalConfigs;
    }

    public function registerBundles(): array
    {
        $filename = $this->configFolder . DIRECTORY_SEPARATOR . 'bundles.php';
        if (\is_readable($filename)) {
            return include_once $filename;
        }

        return [
            new FrameworkBundle(),
            new VCRVCRBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->configFolder . DIRECTORY_SEPARATOR . 'config.yml');
        foreach ($this->additionalConfigs as $additionalConfig) {
            $loader->load($additionalConfig);
        }
    }

    protected function getTemporaryDirectory(): string
    {
        if (null === $this->temporaryDirectory) {
            $this->temporaryDirectory = $this->temporaryFilesystem->createTemporaryDirectory();
        }

        return $this->temporaryDirectory;
    }

    public function getCacheDir(): string
    {
        $cacheDir = $this->getTemporaryDirectory() . '/var/cache';

        if (! is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        return realpath($cacheDir);
    }

    public function getLogDir(): string
    {
        $logsDir = $this->getTemporaryDirectory() . '/var/logs';

        if (! is_dir($logsDir)) {
            mkdir($logsDir, 0777, true);
        }

        return realpath($logsDir);
    }

    public function shutdown(): void
    {
        $this->temporaryDirectory = null;

        parent::shutdown();
    }
}
