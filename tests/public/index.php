<?php

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use UMA\Slim\Psr7Hmac\SecretProvider\HardcodedSecretProvider;
use UMA\Slim\Psr7Hmac\KeyProvider\NullKeyProvider;
use UMA\Slim\Psr7Hmac\Handler\UnauthenticatedHandler;
use UMA\Slim\Psr7Hmac\Psr7HmacAuthentication;

require __DIR__ . '/../../vendor/autoload.php';

$cnt = new Container(['settings' => ['displayErrorDetails' => true]]);

$cnt['hmac_secret'] = 'a_secret';

$cnt[Psr7HmacAuthentication::class] = function ($cnt) {
    return new Psr7HmacAuthentication(
        new NullKeyProvider,
        new HardcodedSecretProvider($cnt['hmac_secret']),
        new UnauthenticatedHandler
    );
};

$app = new \Slim\App($cnt);

$app->get('/foo', function (Request $request, Response $response) {
    $response->getBody()->write("Successful authentication!\n");

    return $response->withStatus(202);
})->add(Psr7HmacAuthentication::class);

$app->run();
