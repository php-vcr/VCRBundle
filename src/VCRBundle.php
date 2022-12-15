<?php
declare(strict_types = 1);

namespace VCR\VCRBundle;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use VCR\Videorecorder;

class VCRBundle extends Bundle
{
    public function boot(): void
    {
        $cassettePath = $this->container->getParameter('vcr.cassette.path');

        if (!is_dir($cassettePath)) {
            $fs = new Filesystem();
            $fs->mkdir($cassettePath);
        }

        if ($this->isEnabled()) {
            $recorder     = $this->getVideoRecorder();
            $cassetteName = $this->container->getParameter('vcr.cassette.name');

            $recorder->turnOn();
            $recorder->insertCassette($cassetteName);
        }
    }

    public function shutdown(): void
    {
        if ($this->isEnabled()) {
            $this->getVideoRecorder()->turnOff();
        }
    }

    private function isEnabled(): bool
    {
        try {
            return $this->container->getParameter('vcr.enabled');
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    private function getVideoRecorder(): Videorecorder
    {
        return $this->container->get('vcr.recorder');
    }
}
