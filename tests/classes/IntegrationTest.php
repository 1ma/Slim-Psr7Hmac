<?php

namespace UMA\Slim\Tests\Psr7Hmac;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use UMA\Psr7Hmac\Signer;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    private $http;

    protected function setUp()
    {
        $this->http = new Client([RequestOptions::HTTP_ERRORS => false]);
    }

    public function testOk()
    {
        $signedRequest = (new Signer('a_secret'))
            ->sign(new Request('GET', 'http://slim-psr7hmac.test/foo'));

        $response = $this->http->send($signedRequest);

        self::assertSame(202, $response->getStatusCode());
    }

    public function testKo()
    {
        $request = new Request('GET', 'http://slim-psr7hmac.test/foo');

        $response = $this->http->send($request);

        self::assertSame(401, $response->getStatusCode());
    }
}
