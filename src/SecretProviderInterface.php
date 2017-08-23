<?php

namespace UMA\Slim\Psr7Hmac;

/**
 * Contract for classes that find secrets associated
 * to given keys. The middleware then use these secrets
 * to verify the incoming request with HMAC.
 */
interface SecretProviderInterface
{
    /**
     * Retrieves the secret associated to a given key. If no such
     * secret exists the implementer must return null.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getSecretFor($key);
}
