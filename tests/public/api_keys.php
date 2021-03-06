<?php

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use UMA\Slim\Psr7Hmac\KeyProvider\HeaderKeyProvider;
use UMA\Slim\Psr7Hmac\Psr7HmacAuthentication;
use UMA\Slim\Psr7Hmac\SecretProvider\KeyValueSecretProvider;
use UMA\Slim\Psr7Hmac\SecretProviderInterface;
use UMA\Slim\Tests\Psr7Hmac\Integration\TestingHandler;

require __DIR__ . '/../../vendor/autoload.php';

$cnt = new Container(['settings' => ['displayErrorDetails' => true]]);

$cnt['secrets_list'] = [
    '1234' => '5678',
    'abcd' => 'efgh'
];

$cnt[SecretProviderInterface::class] = function ($cnt) {
    return new KeyValueSecretProvider($cnt['secrets_list']);
};

$cnt[Psr7HmacAuthentication::class] = function ($cnt) {
    return new Psr7HmacAuthentication(
        new HeaderKeyProvider,
        $cnt[SecretProviderInterface::class],
        new TestingHandler
    );
};

$app = new \Slim\App($cnt);

$app->get('/protected', function (Request $request, Response $response) {
    $response->getBody()->write(
        "Successfully authenticated as '{$request->getAttribute('Authed-As')}'!\n"
    );

    return $response->withStatus(202);
})->add(Psr7HmacAuthentication::class);

$app->run();
