<?php

namespace UMA\Slim\Tests\Psr7Hmac\Integration;

use GuzzleHttp\Psr7\Request;
use UMA\Psr7Hmac\Signer;

class ApiKeysTest extends IntegrationTestCase
{
    public function testSuccessfulAuthentication()
    {
        $signedRequest = (new Signer('5678'))
            ->sign(new Request('GET', 'http://api-keys.test/protected', ['Api-Key' => '1234']));

        $response = $this->http->send($signedRequest);

        self::assertSame(202, $response->getStatusCode());
        self::assertSame("Successfully authenticated as '1234'!\n", (string) $response->getBody());
    }

    public function testRequestWithoutApiKeyHeader()
    {
        $signedRequest = (new Signer('5678'))
            ->sign(new Request('GET', 'http://api-keys.test/protected'));

        $response = $this->http->send($signedRequest);

        self::assertSame(410, $response->getStatusCode());
    }

    public function testRequestWithMadeUpApiKey()
    {
        $signedRequest = (new Signer('5678'))
            ->sign(new Request('GET', 'http://api-keys.test/protected', ['Api-Key' => 'wololo']));

        $response = $this->http->send($signedRequest);

        self::assertSame(411, $response->getStatusCode());
    }

    public function testRequestWithTamperedHeader()
    {
        $signedRequest = (new Signer('5678'))
            ->sign(new Request('GET', 'http://api-keys.test/protected', ['Api-Key' => '1234', 'Accept' => 'text/html']))
            ->withHeader('Accept', 'application/json');

        $response = $this->http->send($signedRequest);

        self::assertSame(412, $response->getStatusCode());
    }

    public function testRequestWithTamperedBody()
    {
        /** @var Request $signedRequest */
        $signedRequest = (new Signer('5678'))
            ->sign(new Request('GET', 'http://api-keys.test/protected', ['Api-Key' => '1234'], 'hello'));

        $response = $this->http->send(
            $this->overrideBodyFrom($signedRequest, 'no, bye')
        );

        self::assertSame(412, $response->getStatusCode());
    }
}
