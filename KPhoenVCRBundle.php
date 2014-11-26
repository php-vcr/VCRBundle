<?php

namespace KPhoen\VCRBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use KPhoen\VCRBundle\VCR\VCRFactory;

class KPhoenVCRBundle extends Bundle
{
    public function boot()
    {
        $cassettePath = $this->container->getParameter('kphoen.vcr.cassette_path');

        if (!is_dir($cassettePath)) {
            mkdir($cassettePath);
        }

        $recorder = $this->container->get('kphoen.vcr.recorder');
        $recorder->configure()->setStorage('json');
        $recorder->configure()->setCassettePath($cassettePath);

        $recorder->turnOn();
        $recorder->insertCassette('kphoen_vcr');
    }
}
