<?php

namespace KPhoen\VCRBundle\VCR;

use VCR\Request;
use VCR\Videorecorder as BaseRecorder;

class LoggedVideoRecorder extends BaseRecorder
{
    private $log = array();

    /**
     * {@inheritDoc}
     */
    public function handleRequest(Request $request)
    {
        $response = parent::handleRequest($request);

        $this->log[] = array(
            'request'  => $request->toArray(),
            'response' => $response->toArray(),
        );

        return $response;
    }

    public function getLog()
    {
        return $this->log;
    }
}
