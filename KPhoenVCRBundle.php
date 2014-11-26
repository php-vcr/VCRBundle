<?php

namespace KPhoen\VCRBundle;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use KPhoen\VCRBundle\VCR\VCRFactory;

class KPhoenVCRBundle extends Bundle
{
    public function boot()
    {
        $cassettePath = $this->container->getParameter('kphoen.vcr.cassette.path');

        if (!is_dir($cassettePath)) {
            $fs = new Filesystem();
            $fs->mkdir($cassettePath);
        }
    }
}
