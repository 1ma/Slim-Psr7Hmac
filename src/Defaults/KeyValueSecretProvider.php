<?php

namespace UMA\Slim\Psr7Hmac\Defaults;

use UMA\Slim\Psr7Hmac\SecretProviderInterface;

/**
 * Simple implementation of the SecretProviderInterface that
 * returns secrets from a hardcoded map of key -> secret entries.
 */
class KeyValueSecretProvider implements SecretProviderInterface
{
    /**
     * @var string[]
     */
    private $secrets;

    /**
     * @param string[] $keyValueMap
     */
    public function __construct(array $keyValueMap = [])
    {
        $this->secrets = $keyValueMap;
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
