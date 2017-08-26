<?php

namespace UMA\Slim\Tests\Psr7Hmac\Unit;

use UMA\Slim\Psr7Hmac\SecretProvider\PDOSecretProvider;

class PDOSecretProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testIt()
    {
        $pdo = new \PDO('sqlite::memory:');

        $pdo->exec('
          CREATE TABLE api_clients (
            api_key TEXT UNIQUE NOT NULL,
            secret  TEXT        NOT NULL
          )
        ');

        $pdo->exec("
          INSERT INTO api_clients (api_key, secret) VALUES
            ('1234', '5678'), ('abcd', 'efgh')
        ");

        $secretProvider = new PDOSecretProvider($pdo, 'api_clients', 'api_key', 'secret');

        self::assertSame('5678', $secretProvider->getSecretFor('1234'));
        self::assertSame(null, $secretProvider->getSecretFor('wololo'));
        self::assertSame('efgh', $secretProvider->getSecretFor('abcd'));
    }
}
