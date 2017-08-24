<?php

namespace UMA\Slim\Psr7Hmac\SecretProvider;

use UMA\Slim\Psr7Hmac\SecretProviderInterface;

/**
 * SecretProviderInterface implementation that leverages
 * the PDO class to retrieve secrets from a relational database.
 */
class PDOSecretProvider implements SecretProviderInterface
{
    const SUPPORTED_DRIVERS = ['mysql', 'pgsql', 'sqlite'];

    /**
     * @var \PDOStatement
     */
    private $stmt;

    /**
     * @param \PDO   $pdo    An open PDO link to a database containing a table with authentication secrets
     * @param string $table  Name of the table holding the secrets
     * @param string $column Name of the column where secrets are stored
     */
    public function __construct(\PDO $pdo, $table, $column)
    {
        $pdoDriver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

        if (!in_array($pdoDriver, self::SUPPORTED_DRIVERS)) {
            throw new \RuntimeException("Attempted to use an unsupported PDO driver: $pdoDriver");
        }

        $this->stmt = $pdo->prepare("
          SELECT $column
          FROM $table
          WHERE $column = :key
          LIMIT 1
        ");
    }

    /**
     * @param string $key
     *
     * @return string|null
     *
     * @throws \PDOException If PDO::ATTR_ERRMODE is set to ERRMODE_EXCEPTION and
     *                       the DB link goes down after instantiating the class.
     */
    public function getSecretFor($key)
    {
        $this->stmt->bindValue('key', $key, \PDO::PARAM_STR);

        $this->stmt->execute();

        $secret = $this->stmt->fetch(\PDO::FETCH_COLUMN);

        return false === $secret ?
            null : $secret;
    }
}
