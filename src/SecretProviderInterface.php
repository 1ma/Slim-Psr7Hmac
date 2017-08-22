<?php

namespace UMA\Slim\Psr7Hmac;

interface SecretProviderInterface
{
    /**
     * @param string $key
     *
     * @return string|null
     */
    public function getSecretFor($key);
}
