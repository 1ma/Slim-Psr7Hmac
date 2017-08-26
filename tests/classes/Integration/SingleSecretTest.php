<?php

namespace UMA\Slim\Tests\Psr7Hmac\Integration;

use GuzzleHttp\Psr7\Request;
use UMA\Psr7Hmac\Signer;

class SingleSecretTest extends IntegrationTestCase
{
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
            ->sign(new Request('GET', 'http://single-secret.test/protected'))
            ->withHeader('Accept', 'text/html');

        $response = $this->http->send($signedRequest);

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
            ->sign(new Request('GET', 'http://single-secret.test/protected', ['Accept' => 'text/html']))
            ->withHeader('Accept', 'application/json');

        $response = $this->http->send($signedRequest);

        self::assertSame(401, $response->getStatusCode());
    }

    public function testTamperedBody()
    {
        /** @var Request $signedRequest */
        $signedRequest = (new Signer('a_secret'))
            ->sign(new Request('GET', 'http://single-secret.test/protected', [], 'hello'));

        $response = $this->http->send(
            $this->overrideBodyFrom($signedRequest, 'no, bye')
        );

        self::assertSame(401, $response->getStatusCode());
    }
}
