<?php

namespace UMA\Slim\Psr7Hmac\Defaults;

use UMA\Slim\Psr7Hmac\SecretProviderInterface;

/**
 * Simple implementation of the SecretProviderInterface that always returns
 * the same secret regardless of the given key.
 *
 * This SecretProvider can be used along the NullKeyProvider in a scenario where
 * a single secret protects the whole API and the concept of "user" is not even needed.
 */
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
     * {@inheritdoc}
     */
    public function getSecretFor($key)
    {
        return $this->secret;
    }
}
