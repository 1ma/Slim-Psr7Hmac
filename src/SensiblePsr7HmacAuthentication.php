<?php

namespace UMA\Slim\Psr7Hmac;

use UMA\Slim\Psr7Hmac\KeyProvider\HeaderKeyProvider;
use UMA\Slim\Psr7Hmac\Handler\UnauthenticatedHandler;

class SensiblePsr7HmacAuthentication extends Psr7HmacAuthentication
{
    public function __construct(SecretProviderInterface $secretProvider)
    {
        parent::__construct(new HeaderKeyProvider, $secretProvider, new UnauthenticatedHandler);
    }
}
