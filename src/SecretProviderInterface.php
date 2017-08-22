<?php

namespace UMA\Slim\Psr7Hmac;

interface SecretProviderInterface
{
    /**
     * Retrieves the secret for a given key. If no such
     * secret exists the implementer must return null.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getSecretFor($key);
}
