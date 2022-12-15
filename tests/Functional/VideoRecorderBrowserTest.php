<?php
declare(strict_types = 1);

namespace VCR\VCRBundle\Tests\Functional;

use VCR\VCRBundle\VideoRecorderBrowser;

class VideoRecorderBrowserTest extends WebTestCase
{
    protected static $configSubFolder = 'VideoRecorderBrowser';

    public function testVideoRecorderBrowser(): void
    {
        $serviceId = 'test.client.vcr';
        $cassetteName = 'test';
        $kernel = static::bootKernel();
        $container = $kernel->getContainer();
        $basePath = $container->getParameter('vcr.cassette.path');
        $fixturesPath = $basePath . '/' . $cassetteName;

        static::assertTrue(
            $container->has($serviceId),
            'VideoRecorderBrowser (Client) service seems not to be registered.'
        );

        $client = $container->get($serviceId);
        static::assertInstanceOf(VideoRecorderBrowser::class, $client);

        $client->insertVideoRecorderCassette($cassetteName);
        file_get_contents('https://www.google.de');

        static::assertFileExists($fixturesPath);
    }
}
