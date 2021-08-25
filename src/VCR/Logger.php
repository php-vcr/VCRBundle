<?php

namespace VCR\VCRBundle\VCR;

use VCR\Cassette;
use VCR\Request;
use VCR\Response;

class Logger
{
    private $http_requests = [];
    private $playbacks = [];

    public function logHttpRequest(Request $request, Response $response): void
    {
        $this->http_requests[] = [
            'request' => $this->augmentRequest($request->toArray()),
            'response' => $response->toArray(),
        ];
    }

    public function logPlayback(Request $request, Response $response, Cassette $cassette): void
    {
        $this->playbacks[] = [
            'request' => $this->augmentRequest($request->toArray()),
            'response' => $response->toArray(),
            'cassette' => $cassette,
        ];
    }

    public function getHttpRequests(): array
    {
        return $this->http_requests;
    }

    public function getPlaybacks(): array
    {
        return $this->playbacks;
    }

    private function augmentRequest(array $request): array
    {
        $url_info = parse_url($request['url']);
        $query_string = [];
        parse_str(! empty($url_info['query']) ? $url_info['query'] : '', $query_string);

        return array_merge(
            $url_info,
            ['query' => $query_string],
            $request
        );
    }
}
