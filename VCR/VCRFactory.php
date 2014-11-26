<?php

namespace KPhoen\VCRBundle\VCR;

use VCR\Configuration;
use VCR\VCRFactory as BaseFactory;

class VCRFactory extends BaseFactory
{
    /**
     * Returns the same VCRFactory instance on ever call (singleton).
     *
     * @param  Configuration $config (Optional) configuration.
     *
     * @return VCRFactory
     */
    public static function getInstance(Configuration $config = null)
    {
        if (!self::$instance) {
            // @todo: or use 'static' in the parent class
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * @return Videorecorder
     */
    protected function createKPhoenVCRBundleVCRLoggedVideoRecorder()
    {
        return new LoggedVideoRecorder(
            $this->config,
            $this->getOrCreate('VCR\Util\HttpClient'),
            $this
        );
    }
}
