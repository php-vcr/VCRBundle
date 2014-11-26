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
    }
}
