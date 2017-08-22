<?php

namespace UMA\Slim\Psr7Hmac;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Contract for classes that handle request authentication failures.
 */
interface UnauthenticatedHandlerInterface
{
    const ATTR = 'Authentication-Failed';

    /**
     * Implementers are guaranteed that the $request object will have an
     * 'Authentication-Failed' attribute explaining the reason it failed.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response);
}
