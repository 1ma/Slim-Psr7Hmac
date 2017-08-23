<?php

namespace UMA\Slim\Psr7Hmac;

use UMA\Slim\Psr7Hmac\Defaults\ApiKeyProvider;
use UMA\Slim\Psr7Hmac\Defaults\UnauthenticatedHandler;

class SensiblePsr7HmacAuthentication extends Psr7HmacAuthentication
{
    public function __construct(SecretProviderInterface $secretProvider)
    {
        parent::__construct(new ApiKeyProvider, $secretProvider, new UnauthenticatedHandler);
    }
}
