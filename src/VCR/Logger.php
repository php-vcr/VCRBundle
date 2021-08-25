<?php

namespace VCR\VCRBundle\VCR;

use VCR\Cassette;
use VCR\Request;
use VCR\Response;

class Logger
{
    private $http_requests = array();
    private $playbacks     = array();

    public function logHttpRequest(Request $request, Response $response)
    {
        $this->http_requests[] = array(
            'request'  => $this->augmentRequest($request->toArray()),
            'response' => $response->toArray(),
        );
    }

    public function logPlayback(Request $request, Response $response, Cassette $cassette)
    {
        $this->playbacks[] = array(
            'request'  => $this->augmentRequest($request->toArray()),
            'response' => $response->toArray(),
            'cassette' => $cassette,
        );
    }

    public function getHttpRequests()
    {
        return $this->http_requests;
    }

    public function getPlaybacks()
    {
        return $this->playbacks;
    }

    private function augmentRequest(array $request)
    {
        $url_info = parse_url($request['url']);
        $query_string = array();
        parse_str(!empty($url_info['query']) ? $url_info['query'] : '', $query_string);

        return array_merge(
            $url_info,
            array('query' => $query_string),
            $request
        );
    }
}
