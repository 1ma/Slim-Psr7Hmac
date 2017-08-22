<?php

namespace UMA\Slim\Psr7Hmac\Defaults;

use UMA\Slim\Psr7Hmac\SecretProviderInterface;

class KeyValueSecretProvider implements SecretProviderInterface
{
    /**
     * @var string[]
     */
    private $secrets = [];

    /**
     * @param string $key
     * @param string $secret
     */
    public function addSecret($key, $secret)
    {
        $this->secrets[$key] = $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecretFor($key)
    {
        return isset($this->secrets[$key]) ?
            $this->secrets[$key] : null;
    }
}
