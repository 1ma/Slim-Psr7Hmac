<?php

namespace UMA\Slim\Psr7Hmac\Defaults;

use UMA\Slim\Psr7Hmac\SecretProviderInterface;

class HardcodedSecretProvider implements SecretProviderInterface
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @param string $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @param null $key
     *
     * @return string
     */
    public function getSecretFor($key = null)
    {
        return $this->secret;
    }
}
