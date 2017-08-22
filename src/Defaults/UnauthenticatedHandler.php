<?php

namespace UMA\Slim\Psr7Hmac\Defaults;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UMA\Psr7Hmac\Specification;
use UMA\Slim\Psr7Hmac\UnauthenticatedHandlerInterface;

/**
 * The default UnauthenticatedHandler returns an "401 Unauthorized" HTTP response (meaning
 * the identity of the requester could not be established) as per the RFC 2617 spec.
 *
 * @see https://tools.ietf.org/html/rfc2617#section-3.2.1
 */
class UnauthenticatedHandler implements UnauthenticatedHandlerInterface
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $response
            ->withStatus(401)
            ->withHeader('WWW-Authenticate', trim(Specification::AUTH_PREFIX));
    }
}
