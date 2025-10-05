<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;

abstract class AbstractModel
{
    abstract public static function getTableName(): string;

    abstract public static function getColumns(): array;

    public static function primaryKey(): string
    {
        return 'id';
    }

    abstract protected static function hydrate(array $data): static;

    /**
     * Prepares an SQL statement using the application's database connection.
     */
    protected static function prepare(string $sql): \PDOStatement
    {
        return Application::db()->prepare($sql);
    }

    /**
     * Persists the current model object to the database using an INSERT statement.
     * @return bool True on successful execution.
     */
    public function save(): bool
    {
        $tableName = static::getTableName();
        $columns = static::getColumns();
        
        $placeholders = array_map(static fn($c) => ":{$c}", $columns);
        $stmt = static::prepare(
            "INSERT INTO {$tableName} (" . implode(", ", $columns) . ")
            VALUES (" . implode(", ", $placeholders) . ");"
        );
        
        // Bind values from object properties
        foreach ($columns as $col) {
            // Note: $this->$col is safe because properties are protected and named correctly
            $stmt->bindValue(":{$col}", $this->$col);
        }
        
        return $stmt->execute();
    }

    /**
     * Finds a single model instance based on a WHERE clause.
     * @param array $where e.g., ['email' => 'test@example.com']
     * @return static|null
     */
    public static function findOne(array $where): ?static
    {
        $tableName = static::getTableName();
        $columns = array_intersect(array_keys($where), static::getColumns());
        
        if (empty($columns)) {
            return null;
        }
        
        $sql = implode(" AND ", array_map(static fn($c) => "{$c} = :{$c}", $columns));
        $stmt = static::prepare("SELECT * FROM {$tableName} WHERE {$sql}");

        foreach ($columns as $col) {
            $stmt->bindValue(":{$col}", $where[$col]);
        }
        
        $stmt->execute();
        
        $data = $stmt->fetch();
        return $data ? static::hydrate($data) : null;
    }

    /**
     * Updates selected attributes in the database and syncs them to the object.
     *
     * @param array $params Associative array of column_name => value to update.
     * @return static|null Returns the updated object or null on failure.
     */
    public function update(array $params): ?static
    {
        $tableName = static::getTableName();
        $primaryKeyName = static::primaryKey();
        $colsToUpdate = array_intersect(static::getColumns(), array_keys($params));
        $colsToUpdate = array_filter($colsToUpdate, static fn($c) => $c !== $primaryKeyName);

        if (empty($colsToUpdate)) {
            return $this;
        }

        $setClauses = implode(", ", array_map(static fn($c) => "{$c} = :{$c}", $colsToUpdate));
        $sql = "UPDATE {$tableName}
                SET {$setClauses}
                WHERE {$primaryKeyName} = :pk_value";

        $stmt = static::prepare($sql);

        foreach ($colsToUpdate as $col) {
            $stmt->bindValue(":$col", $params[$col]);
        }

        $primaryKeyValue = $this->{$primaryKeyName};
        $stmt->bindValue(':pk_value', $primaryKeyValue);

        if (!$stmt->execute()) {
            // Log the error
            error_log("Database update failed for table '{$tableName}' with PK '{$primaryKeyValue}'");
            return null;
        }

        return $this->syncAttributes($params);
    }

    /**
     * Syncs the object's properties with the successfully updated parameters.
     * This method must be called after a successful database update.
     *
     * @param array $params The array of successfully updated column_name => value pairs.
     * @return $this
     */
    protected function syncAttributes(array $params): static
    {
        foreach ($params as $key => $value) {
            if (property_exists($this, $key) && $key !== static::primaryKey()) {
                echo "before errorr";
                
                if (is_int($this->{$key})) {
                    $this->$key = (int) $value;
                } elseif (is_bool($this->{$key})) {
                    $this->$key = (bool) $value;
                } else {
                    $this->$key = $value;
                }
            }
        }
        return $this;
    }
}
