<?php

namespace UMA\Slim\Tests\Psr7Hmac\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use UMA\Psr7Hmac\Signer;

class SingleSecretTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    private $http;

    protected function setUp()
    {
        $this->http = new Client([RequestOptions::HTTP_ERRORS => false]);
    }

    public function testSignedRequest()
    {
        $signedRequest = (new Signer('a_secret'))
            ->sign(new Request('GET', 'http://single-secret.test/protected'));

        $response = $this->http->send($signedRequest);

        self::assertSame(202, $response->getStatusCode());
    }

    public function testAddingExtraHeadersToSignedRequest()
    {
        $signedRequest = (new Signer('a_secret'))
            ->sign(new Request('GET', 'http://single-secret.test/protected'));

        $response = $this->http->send(
            $signedRequest->withHeader('Accept', 'text/html')
        );

        self::assertSame(202, $response->getStatusCode());
    }

    public function testUnsignedRequest()
    {
        $request = new Request('GET', 'http://single-secret.test/protected');

        $response = $this->http->send($request);

        self::assertSame(401, $response->getStatusCode());
    }

    public function testTamperedHeader()
    {
        $signedRequest = (new Signer('a_secret'))
            ->sign(new Request('GET', 'http://single-secret.test/protected', ['Accept' => 'text/html']));

        $response = $this->http->send(
            $signedRequest->withHeader('Accept', 'application/json')
        );

        self::assertSame(401, $response->getStatusCode());
    }
}
