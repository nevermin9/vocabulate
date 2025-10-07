<?php
declare(strict_types=1);

namespace App\Core;

use App\Attributes\Container\Singleton;
use App\Traits\FilesystemReaderTrait;

/**
* @mixin \PDO
*/
#[Singleton]
class DB
{
    use FilesystemReaderTrait;

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

    /**
     * Executes database migrations based on the parsed command-line arguments.
     *
     * This method reads migration files, instantiates migration classes, and calls
     * the appropriate method ('up' or 'down') based on the command.
     *
     * @param array $args An associative array containing the parsed command-line arguments.
     * It should have a 'command' key, and may optionally include
     * 'rollbackAll' or 'target' keys.
     * - 'command' (string): The migration command to execute, either 'up' or 'rollback'.
     * - 'rollbackAll' (bool, optional): If true, all migrations will be rolled back.
     * - 'target' (int, optional): The target version to rollback to.
     * @return void
     */
    public function runMigrations(string $dir, array $args)
    {
        $this->createMigrationsTable();
        if ($args['command'] === 'up') {
            $this->runMigrationsUp($dir);
        } else {
            $this->runMigrationsDown($dir, target: $args['target'] ?? 0);
        }

    }

    protected function runMigrationsUp(string $dir): void
    {
        $filenamesList = $this->getOrderedFilenamesList($dir);
        $applied = $this->getAppliedMigrations();
        $newMigrations = [];

        foreach ($filenamesList as $filename) {
            $className = pathinfo($filename, PATHINFO_FILENAME);

            if (in_array($className, $applied)) {
                continue;
            }

            $fullClassName = "\\App\\Database\\Migrations\\".$className;

            if (class_exists($fullClassName)) {
                $migration = new $fullClassName;
                $this->log("Applying migration " . $className);
                
                try {
                    $migration->up($this);
                    $this->log("Migration " . $className . " is applied");
                    $newMigrations[] = $className;
                } catch (\Throwable $e) {
                    $this->log("Migration " . $className . " failed.");
                    throw $e;
                }
            } else {
                $this->log("Error: Class '" . $fullClassName . "' not found in file " . $file->getPathname());
            }
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
            $this->log("Migrations are saved.");
        } else {
            $this->log("No migrations to apply.");
        }
    }

    protected function runMigrationsDown(string $dir, int $target = 0): void
    {
        $migrations = $this->getMigrationsGreaterThanTarget($dir, $target);
        $applied = $this->getAppliedMigrations();
        $migrationsToDelete = [];

        foreach ($migrations as $filename) {
            $className = pathinfo($filename, PATHINFO_FILENAME);

            if (! in_array($className, $applied)) {
                continue;
            }

            $fullClassName = "\\App\\Database\\Migrations\\".$className;

            if (class_exists($fullClassName)) {
                $migration = new $fullClassName;
                $this->log("Rolling back migration " . $className);

                try {
                    $migration->down($this);
                    $this->log("Migration " . $className . " is rolled back.");
                    $migrationsToDelete[] = $className;
                } catch (\Throwable $e) {
                    $this->log("Migration " . $className . " is failed. Rolling back...");
                    throw $e;
                }
            } else {
                $this->log("Error: Class '" . $fullClassName . "' not found in file " . $file->getPathname());
            }
        }

        if (!empty($migrationsToDelete)) {
            $this->deleteMigrations($migrationsToDelete);
            $this->log("Migrations are deleted.");
        } else {
            $this->log("No migrations to rollback.");
        }
    }

    protected function getMigrationsGreaterThanTarget(string $dir, int $target): array
    {
        $filenamesList = $this->getOrderedFilenamesList($dir);

        $migrations = [];

        $pattern = '/^m(\d{4})_.*$/';

        foreach ($filenamesList as $filename) {
            if (preg_match($pattern, $filename, $matches)) {
                $version = (int) $matches[1];

                if ($version > $target) {
                    $migrations[] = $filename;
                }
            }
        }

        return $migrations;
    }

    protected function createMigrationsTable(): void
    {
        $stmt = $this->pdo->prepare(
            "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );"
        );
        $stmt->execute();
    }

    protected function deleteMigrations(array $migrations): void
    {
        $placeholders = implode(', ', array_fill(0, count($migrations), '?'));
        $stmt = $this->pdo->prepare(
            "DELETE FROM migrations WHERE migration IN ($placeholders);"
        );
        $stmt->execute($migrations);
    }


    protected function saveMigrations(array $migrations): void
    {
        $placeholders = implode(', ', array_fill(0, count($migrations), '(?)'));
        $stmt = $this->pdo->prepare(
            "INSERT INTO migrations (migration) VALUES $placeholders;"
        );
        $stmt->execute($migrations);
    }

    protected function getAppliedMigrations(): array
    {
        $stmt = $this->pdo->prepare("SELECT migration FROM migrations;");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    protected function log(string $message): void
    {
        echo "[".date("Y-m-d H:i:s")."] - " . $message . PHP_EOL;
    }

}
