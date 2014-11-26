VCRBundle
=========

Integrates [php-vcr](https://github.com/php-vcr/php-vcr) into Symfony and its
web profiler.

## Installation

Install the behavior adding `php-vcr/vcr-bundle` to your composer.json or
from CLI:

```bash
php composer.phar require php-vcr/vcr-bundle
```

And declare the bundle in your `app/AppKernel.php` file:

```php
public function registerBundles()
{
    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        $bundles[] = new VCR\VCRBundle\VCRVCRBundle();
    }
}
```

## Configuration reference

```yaml
vcrvcr:
    cassette:
        path:   '%kernel.cache_dir%/vcr'
        format: json
        name:   vcr
```

## Credits

  * [Kévin Gomez](http://github.com/K-Phoen/)
  * [Ludovic Fleury](https://github.com/ludofleury) - to whom I borrowed the
    design of the web profiler part from its [GuzzleBundle](https://github.com/ludofleury/GuzzleBundle/).

## License

This bundle is released under the MIT license.
