<?php
declare(strict_types = 1);

namespace VCR\VCRBundle\Tests\Functional;

use VCR\VCRBundle\Test\VCRTestCaseTrait;
use VCR\VCRBundle\VideoRecorderBrowser;

class VCRTestCaseTraitTest extends WebTestCase
{
    use VCRTestCaseTrait;

    protected static $configSubFolder = 'VideoRecorderBrowser';
    protected $ignoredTestSuiteNamespacePrefix = 'VCR\\VCRBundle\\Tests\\';
    protected $testSuitePath = 'Functional/VCRTestCaseTraitTest';

    public function test(): void
    {
        $kernel = static::bootKernel();
        $container = $kernel->getContainer();

        $basePath = $container->getParameter('vcr.cassette.path');
        $testName = 'test';

        $fixturePath = implode('/', [$basePath, $this->testSuitePath, $testName]);

        $this->enableVideoRecorder($container);
        $this->insertVideoRecorderCassette($container);

        file_get_contents('https://www.google.de');

        static::assertFileExists($fixturePath);
    }

    public function testVideoRecorderBrowser(): void
    {
        $client = static::createVideoRecorderClient();
        static::assertInstanceOf(VideoRecorderBrowser::class, $client);

        $container = $client->getKernel()->getContainer();
        $basePath = $container->getParameter('vcr.cassette.path');
        $testName = 'testVideoRecorderBrowser';
        $fixturePath = implode('/', [$basePath, $this->testSuitePath, $testName]);

        file_get_contents('https://www.google.de');

        static::assertFileExists($fixturePath);
    }
}
