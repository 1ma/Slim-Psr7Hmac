<?php

namespace UMA\Slim\Psr7Hmac;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Contract for classes that handle request authentication failures.
 */
interface UnauthenticatedHandlerInterface
{
    /**
     * The KeyProvider could not retrieve a key from
     * the incoming request.
     */
    const ERR_NO_KEY = 0;

    /**
     * The SecretProvider could not find a secret matching
     * the key received in the request (i.e. is a made up key).
     */
    const ERR_NO_SECRET = 1;

    /**
     * The request might have been tampered in-flight,
     * or a client is making up the value for the
     * 'Authentication' header.
     */
    const ERR_BROKEN_SIG = 2;

    /**
     * $reason will always be one of the error codes defined in this contract.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param int                    $reason
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $reason);
}
