<?php

namespace UMA\Slim\Psr7Hmac\KeyProvider;

use Psr\Http\Message\ServerRequestInterface;
use UMA\Slim\Psr7Hmac\KeyProviderInterface;

/**
 * Most common implementation of the KeyProviderInterface.
 *
 * This KeyProvider retrieves the Api Key from one of the headers
 * of the HTTP request.
 */
class HeaderKeyProvider implements KeyProviderInterface
{
    const DEFAULT_HEADER = 'Api-Key';

    /**
     * @var string
     */
    private $apiKeyHeader;

    /**
     * @param string $apiKeyHeader
     */
    public function __construct($apiKeyHeader = self::DEFAULT_HEADER)
    {
        $this->apiKeyHeader = $apiKeyHeader;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeyFrom(ServerRequestInterface $request)
    {
        $apiKey = $request->getHeaderLine($this->apiKeyHeader);

        return '' !== $apiKey ?
            $apiKey : null;
    }
}
