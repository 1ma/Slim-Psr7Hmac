<?php

namespace UMA\Slim\Psr7Hmac\Defaults;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UMA\Slim\Psr7Hmac\UnauthorizedHandlerInterface;

class UnauthorizedHandler implements UnauthorizedHandlerInterface
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $response->withStatus(401);
    }
}
