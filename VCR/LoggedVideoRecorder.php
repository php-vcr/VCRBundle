<?php

namespace KPhoen\VCRBundle\VCR;

use Symfony\Component\HttpFoundation\Response;
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
            'request'  => $this->augmentRequest($request->toArray()),
            'response' => $this->augmentResponse($response->toArray()),
        );

        return $response;
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

    private function augmentResponse(array $response)
    {
        $status_text = isset(Response::$statusTexts[$response['status']]) ? Response::$statusTexts[$response['status']] : 'Unknown';

        return array_merge(
            array('status_text' => $status_text),
            $response
        );
    }

    public function getLog()
    {
        return $this->log;
    }
}
