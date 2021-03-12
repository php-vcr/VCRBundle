<?php

namespace VCR\VCRBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use VCR\VCRBundle\VCR\Logger;

class VCRDataCollector extends DataCollector
{
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $requests  = $this->logger->getHttpRequests();
        $playbacks = $this->logger->getPlaybacks();

        $this->data = array(
            'requests'   => $requests,
            'playbacks'  => $playbacks,
            'count'      => count($requests) + count($playbacks),
        );
    }

    public function getRequestsLogs()
    {
        return $this->data['requests'];
    }

    public function getPlaybacks()
    {
        return $this->data['playbacks'];
    }

    public function getPlaybacksCount()
    {
        return count($this->data['playbacks']);
    }

    public function getRequestsCount()
    {
        return count($this->data['requests']);
    }

    public function getCount()
    {
        return $this->data['count'];
    }

    public function getName()
    {
        return 'vcr_collector';
    }

    public function reset()
    {
        $this->data['requests'] = [];
        $this->data['playbacks'] = [];
        $this->data['count'] = 0;
    }
}
