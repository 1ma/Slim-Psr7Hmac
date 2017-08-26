<?php

namespace UMA\Slim\Tests\Psr7Hmac\Integration;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UMA\Slim\Psr7Hmac\UnauthenticatedHandlerInterface;

/**
 * Unauthenticated handler for integration testing purposes.
 */
class TestingHandler implements UnauthenticatedHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $reason)
    {
        switch ($reason) {
            case UnauthenticatedHandlerInterface::ERR_NO_KEY:
                return $response->withStatus(410);
                break;

            case UnauthenticatedHandlerInterface::ERR_NO_SECRET:
                return $response->withStatus(411);
                break;

            case UnauthenticatedHandlerInterface::ERR_BROKEN_SIG:
                return $response->withStatus(412);
                break;

            default:
                throw new \LogicException(
                    "That 1ma fella is a frikin' liar! Got undocumented error code: $reason"
                );
        }
    }
}
