<?php

namespace UMA\Slim\Psr7Hmac;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UMA\Psr7Hmac\Verifier;

class Psr7HmacAuthentication
{
    const DEFAULT_API_KEY_HEADER = 'Api-Key';

    /**
     * @var KeyProviderInterface
     */
    private $keyProvider;

    /**
     * @var SecretProviderInterface
     */
    private $secretProvider;

    /**
     * @var UnauthenticatedHandlerInterface
     */
    private $unauthenticatedHandler;

    /**
     * @param SecretProviderInterface         $secretProvider
     * @param KeyProviderInterface            $keyProvider
     * @param UnauthenticatedHandlerInterface $unauthenticatedHandler
     */
    public function __construct(
        KeyProviderInterface $keyProvider,
        SecretProviderInterface $secretProvider,
        UnauthenticatedHandlerInterface $unauthenticatedHandler
    ) {
        $this->keyProvider = $keyProvider ;
        $this->secretProvider = $secretProvider;
        $this->unauthenticatedHandler = $unauthenticatedHandler;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable               $next
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        if (null === $key = $this->keyProvider->getKeyFrom($request)) {
            return $this->halt($request, $response, 'Could not retrieve key from request');
        }

        if (null === $secret = $this->secretProvider->getSecretFor($key)) {
            return $this->halt($request, $response, "Could not find secret for '{$key}' key");
        }

        if (false === (new Verifier)->verify($request, $secret)) {
            return $this->halt($request, $response, 'Broken HMAC signature!');
        }

        return $next($request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param string                 $reason
     *
     * @return ResponseInterface
     */
    private function halt(ServerRequestInterface $request, ResponseInterface $response, $reason)
    {
        $unauthenticatedHandler = $this->unauthenticatedHandler;

        return $unauthenticatedHandler(
            $request->withAttribute(UnauthenticatedHandlerInterface::ATTR, $reason), $response
        );
    }
}
