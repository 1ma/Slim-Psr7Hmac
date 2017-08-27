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
     * @param \PDO   $pdo          An open PDO link to a database containing a table with authentication secrets
     * @param string $table        Name of the table holding the key <-> secret relationship
     * @param string $keyColumn    Name of the column where keys are stored
     * @param string $secretColumn Name of the column where secrets are stored
     *
     * @throws \RuntimeException If the $pdo driver is not supported (see SUPPORTED_DRIVERS constant).
     * @throws \PDOException     If the SQL statement cannot be prepared with the supplied table and column names.
     */
    public function __construct(\PDO $pdo, $table, $keyColumn, $secretColumn)
    {
        $pdoDriver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

        if (!in_array($pdoDriver, self::SUPPORTED_DRIVERS)) {
            throw new \RuntimeException("Attempted to use an unsupported PDO driver. Got: $pdoDriver");
        }

        $this->stmt = $pdo->prepare("
          SELECT $secretColumn
            FROM $table
           WHERE $keyColumn = :key
           LIMIT 1
        ");

        // If $pdo is configured as PDO:ERRMODE_SILENT (which is the default) prepare()
        // will not throw a PDOException by itself, so it must be forced.
        if (!$this->stmt instanceof \PDOStatement) {
            throw new \PDOException(
                "Could not prepare SQL statement with supplied data. Got: [$table, $keyColumn, $secretColumn]"
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \PDOException If the prepared statement cannot be executed.
     */
    public function getSecretFor($key)
    {
        $this->stmt->bindValue('key', $key, \PDO::PARAM_STR);

        if (false === $this->stmt->execute()) {
            throw new \PDOException(
                "Could not execute prepared statement. Got error: {$this->stmt->errorInfo()[2]}"
            );
        }

        $secret = $this->stmt->fetch(\PDO::FETCH_COLUMN);

        return false === $secret ?
            null : $secret;
    }
}
