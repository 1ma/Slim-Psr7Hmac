<?php

namespace UMA\Slim\Psr7Hmac\Defaults;

use Psr\Http\Message\ServerRequestInterface;
use UMA\Slim\Psr7Hmac\KeyProviderInterface;

/**
 * Simple implementation of the KeyProviderInterface that always returns the
 * same empty key, regardless of the HTTP request.
 *
 * This KeyProvider can be used along the HardcodedSecretProvider in a scenario
 * where a single secret protects the whole API and the concept of "user" is not even needed.
 */
class NullKeyProvider implements KeyProviderInterface
{
    /**
     * Despite the class name the actual return value is a zero-length string.
     * This is because the middleware considers strings as valid
     * keys, whereas a null would trigger the UnauthenticatedHandler.
     *
     * {@inheritdoc}
     */
    public function getKeyFrom(ServerRequestInterface $request)
    {
        return '';
    }
}
