VCRBundle
=========

Integrates [php-vcr](https://github.com/php-vcr/php-vcr) into Symfony and its
web profiler.
It also provides a VideoRecorderBrowser for testing purpose with extra helper methods handling php-vcr recordings.

<img src="https://cloud.githubusercontent.com/assets/66958/5232274/b841676e-774b-11e4-8f4e-1f3e8cb7739e.png" width="280" height="175" alt="PHP-VCR Symfony web profiler panel"/>
<img src="https://cloud.githubusercontent.com/assets/66958/5232275/b84288d8-774b-11e4-803c-7b72f75e59b0.png" width="280" height="175" alt="PHP-VCR Symfony web profiler panel - request details"/>
<img src="https://cloud.githubusercontent.com/assets/66958/5232276/b84411b2-774b-11e4-93a9-475a0eeede65.png" width="280" height="175" alt="PHP-VCR Symfony web profiler panel - response details"/>

## Installation

Install the behavior adding `php-vcr/vcr-bundle` to your composer.json or
from CLI:

```bash
composer require php-vcr/vcr-bundle
```

And declare the bundle in your `config/bundles.php` file:

```php
<?php
declare(strict_types = 1);

return [
    // ...
    VCR\VCRBundle\VCRBundle::class => ['test' => true],
];
```

## Usage

Enable the required library hooks for your purpose and write test cases.

### VideoRecorderBrowser (without Trait)

```php
<?php
declare(strict_types = 1);

class ExampleTest extends \VCR\VCRBundle\Tests\Functional\WebTestCase
{
    public function test(): void
    {
        $kernel = static::bootKernel();
        /** @var \VCR\VCRBundle\VideoRecorderBrowser $client */
        $client = $kernel->getContainer()->get('test.client.vcr');
        
        $client->insertVideoRecorderCassette('my-test-cassette-name');
        
        // this is an example, normally services inside you project do stuff like this and you trigger them by
        // execute requests via the KernelBrowser client
        file_get_contents('https://www.google.de');
        
        // cassette.path is configured to '%kernel.project_dir%/tests/Fixtures'
        // recordings are written to %kernel.project_dir%/tests/Fixtures/my-test-cassette-name
        // cassette.path + cassetteName (done by inserting the cassette)
    }
}
```

### VideoRecorderBrowser (with Trait)

```php
<?php
declare(strict_types = 1);

namespace MyCompany\MyProject\Tests\Functional;

class ExampleTest extends \VCR\VCRBundle\Tests\Functional\WebTestCase
{
    use \VCR\VCRBundle\Test\VCRTestCaseTrait;

    /**
     * Specify a namespace prefix which should be ignored while generating the base path for this test case. 
     */
    protected $ignoredTestSuiteNamespacePrefix = 'MyCompany\\MyProject\\Tests\\';

    public function test(): void
    {
        /** @var \VCR\VCRBundle\VideoRecorderBrowser $client */
        $client = static::createVideoRecorderClient();
        
        // this is an example, normally services inside you project do stuff like this and you trigger them by
        // execute requests via the KernelBrowser client
        file_get_contents('https://www.google.de');
        
        // cassette.path is configured to '%kernel.project_dir%/tests/Fixtures'
        // recordings are written to %kernel.project_dir%/tests/Fixtures/Functional/ExampleTest/test
        // cassette.path + TestCasePath (- ignoredTestSuiteNamespacePrefix) + TestName
    }
}
```

## Configuration reference

```yaml
vcr:
  enabled: true
  library_hooks:
    stream_wrapper: false
    curl: false
    soap: false
  request_matchers:
    method: true
    url: true
    query_string: true
    host: true
    headers: true
    body: true
    post_fields: true
  cassette:
    type: json
    path: '%kernel.cache_dir%/vcr'
    name: vcr
```

## Credits

* [Kévin Gomez](http://github.com/K-Phoen/)
* [Ludovic Fleury](https://github.com/ludofleury) - to whom I borrowed the
  design of the web profiler part from his [GuzzleBundle](https://github.com/ludofleury/GuzzleBundle/).
* [Simon Hübner](https://github.com/simonhard) - making the bundle SF 5.4 compatible
* [Daniel Hürtgen](https://github.com/higidi) - making the bundle PHP 8 compatible and providing extra TestCase helper

## License

This bundle is released under the MIT license.
