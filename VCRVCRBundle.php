<?php

namespace VCR\VCRBundle;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use VCR\VCRBundle\VCR\VCRFactory;

class VCRVCRBundle extends Bundle
{
    public function boot()
    {
        $cassettePath = $this->container->getParameter('vcr.cassette.path');

        if (!is_dir($cassettePath)) {
            $fs = new Filesystem();
            $fs->mkdir($cassettePath);
        }

        if ($this->container->getParameter('vcr.enabled')) {
            $recorder     = $this->container->get('vcr.recorder');
            $cassetteName = $this->container->getParameter('vcr.cassette.name');

            $recorder->turnOn();
            $recorder->insertCassette($cassetteName);
        }
    }
}
