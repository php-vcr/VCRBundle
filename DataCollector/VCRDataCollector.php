<?php

namespace VCR\VCRBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

use VCR\VCRBundle\VCR\LoggedVideoRecorder;

class VCRDataCollector extends DataCollector
{
    private $recorder;

    public function __construct(LoggedVideoRecorder $recorder)
    {
        $this->recorder = $recorder;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'logs'  => $logs = $this->recorder->getLog(),
            'count' => count($logs),
        );
    }

    public function getRequestsLogs()
    {
        return $this->data['logs'];
    }

    public function getRequestsCount()
    {
        return $this->data['count'];
    }

    public function getName()
    {
        return 'vcr_collector';
    }
}
