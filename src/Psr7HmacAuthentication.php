<?php

namespace UMA\Slim\Psr7Hmac;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UMA\Psr7Hmac\Verifier;
use UMA\Slim\Psr7Hmac\Defaults\UnauthenticatedHandler;

class Psr7HmacAuthentication
{
    const DEFAULT_API_KEY_HEADER = 'Api-Key';

    /**
     * @var SecretProviderInterface
     */
    private $provider;

    /**
     * @var string
     */
    private $apiKeyHeader;

    /**
     * @var UnauthenticatedHandlerInterface
     */
    private $handler;

    /**
     * @param SecretProviderInterface         $provider
     * @param UnauthenticatedHandlerInterface $handler
     * @param string                          $apiKeyHeader
     */
    public function __construct(
        SecretProviderInterface $provider,
        UnauthenticatedHandlerInterface $handler = null,
        $apiKeyHeader = self::DEFAULT_API_KEY_HEADER
    ) {
        $this->provider = $provider;
        $this->apiKeyHeader = $apiKeyHeader;
        $this->handler = null === $handler ?
            new UnauthenticatedHandler : $handler;
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
        if ('' === $apiKey = $request->getHeaderLine($this->apiKeyHeader)) {
            return $this->halt($request, $response, "Missing '{$this->apiKeyHeader}' header in request");
        }

        if (null === $secret = $this->provider->getSecretFor($apiKey)) {
            return $this->halt($request, $response, "Could not find secret for '{$apiKey}' key");
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
        $unauthenticatedHandler = $this->handler;

        return $unauthenticatedHandler(
            $request->withAttribute(UnauthenticatedHandler::ATTR, $reason), $response
        );
    }
}
