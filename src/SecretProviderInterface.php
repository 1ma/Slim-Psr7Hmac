<?php

namespace UMA\Slim\Psr7Hmac;

/**
 * Contract for classes that find secrets used to
 * verify incoming requests.
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
