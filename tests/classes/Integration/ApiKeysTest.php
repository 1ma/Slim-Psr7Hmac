<?php

namespace UMA\Slim\Tests\Psr7Hmac\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use UMA\Psr7Hmac\Signer;

class ApiKeysTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    private $http;

    protected function setUp()
    {
        $this->http = new Client([RequestOptions::HTTP_ERRORS => false]);
    }

    public function testSuccessfulAuthentication()
    {
        $signedRequest = (new Signer('5678'))
            ->sign(new Request('GET', 'http://api-keys.test/protected', ['Api-Key' => '1234']));

        $response = $this->http->send($signedRequest);

        self::assertSame(202, $response->getStatusCode());
        self::assertSame("Successfully authenticated as '1234'!\n", (string) $response->getBody());
    }
}
