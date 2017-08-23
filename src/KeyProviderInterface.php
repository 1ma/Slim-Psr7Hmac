<?php

namespace UMA\Slim\Psr7Hmac;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Contract for classes that extract a key from the
 * HTTP request. The middleware will then use this key to
 * find the appropriate secret for verifying that same request.
 */
interface KeyProviderInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    public function getKeyFrom(ServerRequestInterface $request);
}
