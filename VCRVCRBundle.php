<?php

namespace VCR\VCRBundle;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use VCR\VCRBundle\VCR\VCRFactory;

class VCRVCRBundle extends Bundle
{
    public function boot()
    {
        $container = $this->container;
        $cassettePath = $container->getParameter('vcr.cassette.path');

        if (!is_dir($cassettePath)) {
            $fs = new Filesystem();
            $fs->mkdir($cassettePath);
        }

        if ($container->getParameter('vcr.enabled')) {
            $recorder = $container->get('vcr.recorder');
            $recorder->turnOn();
            $cassetteName = $container->getParameter('vcr.cassette.name');
            $recorder->insertCassette($cassetteName);
        }
    }
}
