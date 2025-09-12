<?php
declare(strict_types=1);

namespace App\Core;

/**
* @mixin \PDO
*/
final class DB
{
    private \PDO $pdo;

    public function __construct(array $config)
    {
        $host = $config['host'];
        $driver = $config['driver'];
        $dbname = $config['name'];
        $user = $config['user'];
        $pass = $config['password'];
        $options = $config['options'] ?? [
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];

        try {
            $this->pdo = new \PDO($driver . ':host=' . $host . ';dbname=' . $dbname, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), $e->getCode());
        }
    }

    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->pdo, $name], $arguments);
    }
}
