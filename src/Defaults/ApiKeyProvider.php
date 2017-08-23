<?php

namespace UMA\Slim\Psr7Hmac\Defaults;

use Psr\Http\Message\ServerRequestInterface;
use UMA\Slim\Psr7Hmac\KeyProviderInterface;

class ApiKeyProvider implements KeyProviderInterface
{
    const DEFAULT_API_KEY_HEADER = 'Api-Key';

    /**
     * @var string
     */
    private $apiKeyHeader;

    public function __construct($apiKeyHeader = self::DEFAULT_API_KEY_HEADER)
    {
        $this->apiKeyHeader = $apiKeyHeader;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeyFrom(ServerRequestInterface $request)
    {
        $apiKey = $request->getHeaderLine($this->apiKeyHeader);

        return '' === $apiKey ?
            null : $apiKey;
    }
}
