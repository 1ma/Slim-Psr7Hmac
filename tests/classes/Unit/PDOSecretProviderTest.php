<?php

namespace UMA\Slim\Tests\Psr7Hmac\Unit;

use UMA\Slim\Psr7Hmac\SecretProvider\PDOSecretProvider;

class PDOSecretProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider pdoProvider
     */
    public function testIt(\PDO $pdo)
    {
        $pdo->exec('
          CREATE TABLE api_clients (
            api_key VARCHAR(16) UNIQUE NOT NULL,
            secret  VARCHAR(16)        NOT NULL
          )
        ');

        $pdo->exec("
          INSERT INTO api_clients (api_key, secret) VALUES
            ('1234', '5678'), ('abcd', 'efgh')
        ");

        $provider = new PDOSecretProvider($pdo, 'api_clients', 'api_key', 'secret');

        self::assertSame('5678', $provider->getSecretFor('1234'));
        self::assertSame(null, $provider->getSecretFor('wololo'));
        self::assertSame('efgh', $provider->getSecretFor('abcd'));

        $pdo->exec('DROP TABLE api_clients');
    }

    /**
     * @dataProvider pdoProvider
     */
    public function testWrongTableInformation(\PDO $pdo)
    {
        $this->expectException(\PDOException::class);

        (new PDOSecretProvider($pdo, 'foo', 'bar', 'baz'))
            ->getSecretFor('wololo');
    }

    public function pdoProvider()
    {
        return [
            'sqlite' => [new \PDO('sqlite::memory:')],
            'mysql' => [new \PDO('mysql:host=mysql;dbname=psr7hmac_test', 'mysql', 'mysql')],
            'pgsql' => [new \PDO('pgsql:host=pgsql;dbname=psr7hmac_test;user=postgres;password=postgres')]
        ];
    }

    public function testUnsupportedDriver()
    {
        $this->expectException(\RuntimeException::class);

        /** @var \PDO|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $pdo = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pdo->expects($this->once())
            ->method('getAttribute')
            ->with(\PDO::ATTR_DRIVER_NAME)
            ->will($this->returnValue('oci'));

        new PDOSecretProvider($pdo, 'api_clients', 'api_key', 'secret');
    }
}
