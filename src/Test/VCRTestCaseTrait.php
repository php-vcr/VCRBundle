<?php
declare(strict_types = 1);

namespace VCR\VCRBundle\Test;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use VCR\VCRBundle\VideoRecorderBrowser;
use VCR\Videorecorder;

/**
 * Trait providing helper functions to work with \PHPUnit\Framework\TestCase in combination with
 * \Symfony\Bundle\FrameworkBundle\Test\WebTestCase and the php-vcr library (&bundle).
 *
 * @method ?string getName(bool $withDataSet)
 * @property ?string $ignoredTestSuiteNamespacePrefix Set a namespace prefix which should be ignored while generating
 *     the cassette path
 */
trait VCRTestCaseTrait
{
    /**
     * @var null|string
     */
    protected $testSuiteName;

    /**
     *
     *
     * @return string The current test suite name
     */
    public function getTestSuiteName(): string
    {
        $this->setUpTestSuiteName();

        return $this->testSuiteName;
    }

    /**
     * @beforeClass
     *
     * @return void
     */
    protected function setUpTestSuiteName(): void
    {
        if (! $this->testSuiteName) {
            $testSuiteName = (new \ReflectionClass($this))->getName();
            if (
                ! empty($this->ignoredTestSuiteNamespacePrefix) &&
                str_starts_with($testSuiteName, $this->ignoredTestSuiteNamespacePrefix)
            ) {
                $testSuiteName = str_replace($this->ignoredTestSuiteNamespacePrefix, '', $testSuiteName);
            }
            $testSuiteNameParts = array_filter(explode('\\', $testSuiteName));
            $this->testSuiteName = implode(DIRECTORY_SEPARATOR, $testSuiteNameParts);
        }
    }

    /**
     * Normalize a video recorder cassette name.
     *
     * @param string $name The name to normalize
     *
     * @return string The normalized cassette name
     */
    protected function normalizeVideoRecorderCassetteName(string $name): string
    {
        return preg_replace(['/\s+/', '/[^a-zA-Z0-9\-_]/'], ['-', ''], $name);
    }

    /**
     * Get the video recorder cassette name from the current test.
     *
     * @param null|string $suffix
     * @param bool $withDataSet With dataset
     *
     * @return string
     */
    protected function getVideoRecorderCassetteName(string $suffix = null, bool $withDataSet = true): string
    {
        $name = $this->getName($withDataSet);
        if (! empty($suffix)) {
            $name .= $suffix;
        }

        return $this->normalizeVideoRecorderCassetteName($name);
    }

    /**
     * Enable VCR VideoRecorder.
     *
     * @param ContainerInterface $container
     * @param string|null $suffix
     *
     * @param bool $withDataSet
     *
     * @return void
     */
    protected function enableVideoRecorder(
        ContainerInterface $container,
        string $suffix = null,
        bool $withDataSet = true
    ): void {
        $videoRecorder = $this->getVideoRecorder($container);
        $videoRecorder->turnOn();
        $this->insertVideoRecorderCassette($container, $suffix, $withDataSet);
    }

    /**
     * Insert cassette into VCR VideoRecorder.
     *
     * @param ContainerInterface $container
     * @param null|string $suffix
     *
     * @param bool $withDataSet
     *
     * @return void
     */
    protected function insertVideoRecorderCassette(
        ContainerInterface $container,
        string $suffix = null,
        bool $withDataSet = true
    ): void {
        $name = $this->getVideoRecorderCassetteName($suffix, $withDataSet);
        $name = implode(
            DIRECTORY_SEPARATOR,
            [
                $this->getTestSuiteName(),
                $name,
            ]
        );

        $videoRecorder = $this->getVideoRecorder($container);

        $videoRecorder->insertCassette($name);
    }

    /**
     * Insert default cassette into VCR VideoRecorder.
     *
     * @param ContainerInterface $container
     *
     * @return void
     */
    protected function insertDefaultVideoRecorderCassette(ContainerInterface $container): void
    {
        $defaultCassetteName = $container->getParameter('vcr.cassette.name');

        $this->getVideoRecorder($container)->insertCassette($defaultCassetteName);
    }

    /**
     * Disable VCR VideoRecorder.
     *
     * @param ContainerInterface $container
     *
     * @return void
     */
    protected function disableVideoRecorder(ContainerInterface $container): void
    {
        $this->getVideoRecorder($container)->turnOff();
    }

    /**
     * @param ContainerInterface $container
     *
     * @return Videorecorder
     */
    protected function getVideoRecorder(ContainerInterface $container): Videorecorder
    {
        return $container->get('vcr.recorder');
    }

    /**
     * Creates a Client supports the VideoRecorder framework.
     *
     * @param array $options An array of options to pass to the createKernel method
     * @param array $server An array of server parameters
     * @param null|string $suffix
     * @param bool $withDataSet
     *
     * @return VideoRecorderBrowser A KernelBrowser instance
     */
    protected function createVideoRecorderClient(
        array $options = [],
        array $server = [],
        string $suffix = null,
        bool $withDataSet = true
    ) {
        if (! \method_exists(\get_called_class(), 'createClient')) {
            throw new \LogicException(
                'Current test case has no static method to createClient(array $options, array $server).'
            );
        }

        /** @var KernelBrowser $client */
        $client = static::createClient($options, $server);
        try {
            /** @var VideoRecorderBrowser $client */
            $client = $client->getKernel()->getContainer()->get('test.client.vcr');
        } catch (ServiceNotFoundException $e) {
            throw new \LogicException('VideoRecorderBrowser not loaded. Did you enable the this bundle?');
        }

        $client->setVideoRecorderCassetteBasePath($this->getTestSuiteName());
        $client->enableVideoRecorder();
        $client->insertVideoRecorderCassette($this->getVideoRecorderCassetteName($suffix, $withDataSet));

        return $client;
    }
}
