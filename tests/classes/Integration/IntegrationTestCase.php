<?php

namespace UMA\Slim\Tests\Psr7Hmac\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\RequestOptions;

abstract class IntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $http;

    protected function setUp()
    {
        $this->http = new Client([RequestOptions::HTTP_ERRORS => false]);
    }

    /**
     * @param Request $request
     * @param string  $newContent
     *
     * @return Request
     */
    protected function overrideBodyFrom(Request $request, $newContent)
    {
        $stream = fopen('php://memory', 'r+');

        fwrite($stream, $newContent);
        rewind($stream);

        return $request->withBody(new Stream($stream));
    }
}
