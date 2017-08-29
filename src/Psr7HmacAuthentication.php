<?php

namespace UMA\Slim\Psr7Hmac;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UMA\Psr7Hmac\Inspector\InspectorInterface;
use UMA\Psr7Hmac\Verifier;

class Psr7HmacAuthentication
{
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
     * @var Verifier
     */
    private $verifier;

    /**
     * @param SecretProviderInterface         $secretProvider
     * @param KeyProviderInterface            $keyProvider
     * @param UnauthenticatedHandlerInterface $unauthenticatedHandler
     * @param InspectorInterface|null         $inspector
     */
    public function __construct(
        KeyProviderInterface $keyProvider,
        SecretProviderInterface $secretProvider,
        UnauthenticatedHandlerInterface $unauthenticatedHandler,
        InspectorInterface $inspector = null
    ) {
        $this->keyProvider = $keyProvider;
        $this->secretProvider = $secretProvider;
        $this->unauthenticatedHandler = $unauthenticatedHandler;
        $this->verifier = new Verifier($inspector);
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
            return $this->halt($request, $response, UnauthenticatedHandlerInterface::ERR_NO_KEY);
        }

        if (null === $secret = $this->secretProvider->getSecretFor($key)) {
            return $this->halt($request, $response, UnauthenticatedHandlerInterface::ERR_NO_SECRET);
        }

        if (false === $this->verifier->verify($request, $secret)) {
            return $this->halt($request, $response, UnauthenticatedHandlerInterface::ERR_BROKEN_SIG);
        }

        return $next($request->withAttribute('Authed-As', $key), $response);
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

        return $unauthenticatedHandler($request, $response, $reason);
    }
}
