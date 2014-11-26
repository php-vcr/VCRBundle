VCRBundle
=========

Integrates php-vcr into Symfony and its web profiler.

## Installation

Install the behavior adding `kphoen/vcr-bundle` to your composer.json or
from CLI:

```bash
php composer.phar require kphoen/vcr-bundle
```

And declare the bundle in your `app/AppKernel.php` file:

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new KPhoen\VCRBundle\KPhoenVCRBundle(),
        // ...
    );
}
```

## Configuration reference

```yaml
k_phoen_vcr:
    cassette:
        path:   '%kernel.cache_dir%/vcr'
        format: json
        name:   vcr
```

## License

This bundle is released under the MIT license.
