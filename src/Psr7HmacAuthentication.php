<?php

namespace UMA\Slim\Psr7Hmac;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UMA\Psr7Hmac\Verifier;

class Psr7HmacAuthentication
{
    const DEFAULT_API_KEY_HEADER = 'Api-Key';

    /**
     * @var SecretProviderInterface
     */
    private $provider;

    /**
     * @var UnauthorizedHandlerInterface
     */
    private $handler;

    /**
     * @var string
     */
    private $apiKeyHeader;

    /**
     * @param SecretProviderInterface      $provider
     * @param UnauthorizedHandlerInterface $handler
     * @param string                       $apiKeyHeader
     */
    public function __construct(
        SecretProviderInterface $provider,
        UnauthorizedHandlerInterface $handler,
        $apiKeyHeader = self::DEFAULT_API_KEY_HEADER
    ) {
        $this->provider = $provider;
        $this->handler = $handler;
        $this->apiKeyHeader = $apiKeyHeader;
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
        $unauthorizedHandler = $this->handler;

        if ('' === $apiKey = $request->getHeaderLine($this->apiKeyHeader)) {
            return $unauthorizedHandler(
                $request->withAttribute('Unauthorized', "Missing '{$this->apiKeyHeader}' header in request"),
                $response
            );
        }

        if (null === $secret = $this->provider->getSecretFor($apiKey)) {
            return $unauthorizedHandler(
                $request->withAttribute('Unauthorized', "Could not find secret for '{$apiKey}' key"),
                $response
            );
        }

        if (false === (new Verifier)->verify($request, $secret)) {
            return $unauthorizedHandler(
                $request->withAttribute('Unauthorized', 'Broken HMAC signature!'),
                $response
            );
        }

        return $next($request, $response);
    }
}
