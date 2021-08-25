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
        $this->reset();
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $requests = $this->logger->getHttpRequests();
        $playbacks = $this->logger->getPlaybacks();

        $this->data = [
            'requests' => $requests,
            'playbacks' => $playbacks,
            'count' => count($requests) + count($playbacks),
        ];
    }

    public function getRequestsLogs(): array
    {
        return $this->data['requests'];
    }

    public function getPlaybacks(): array
    {
        return $this->data['playbacks'];
    }

    public function getPlaybacksCount(): int
    {
        return count($this->data['playbacks']);
    }

    public function getRequestsCount(): int
    {
        return count($this->data['requests']);
    }

    public function getCount(): int
    {
        return $this->data['count'];
    }

    public function getName(): string
    {
        return 'vcr_collector';
    }

    public function reset(): void
    {
        $this->data = [
            'requests' => [],
            'playbacks' => [],
            'count' => 0,
        ];
    }
}
