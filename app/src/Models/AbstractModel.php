<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;
use App\Core\DB;

abstract class AbstractModel
{
    abstract public static function getTableName(): string;

    abstract public static function getColumns(): array;

    abstract public static function fromDatabase(array $data): static;

    public static function primaryKey(): string
    {
        return 'id';
    }

    /**
     * Determines if this model uses auto-incrementing primary key.
     * Override this method to return false for models that generate their own IDs (e.g., UUIDs).
     */
    protected static function usesAutoIncrementPrimaryKey(): bool
    {
        return true;
    }

    /**
     * Returns columns that should be included in INSERT statements.
     * By default, excludes the primary key for auto-increment models.
     */
    protected static function getColumnsForInsert(): array
    {
        $columns = static::getColumns();
        
        if (static::usesAutoIncrementPrimaryKey()) {
            return array_values(array_filter($columns, fn($col) => $col !== static::primaryKey()));
        }
        
        return $columns;
    }

    protected static function db(): DB
    {
        return Application::app()->container->get(DB::class);
    }

    /**
     * Prepares an SQL statement using the application's database connection.
     */
    protected static function prepare(string $sql): \PDOStatement
    {
        return static::db()->prepare($sql);
    }

    /**
     * Persists the current model object to the database using an INSERT statement.
     * @return bool True on successful execution.
     */
    public function save(): bool
    {
        $tableName = static::getTableName();
        $columns = static::getColumnsForInsert();
        
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
        return $data ? static::fromDatabase($data) : null;
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

    public function delete(): bool
    {
        $primaryKeyName = static::primaryKey();
        if (!isset($this->{$primaryKeyName})) {
            return false;
        }

        $tableName = static::getTableName();
        $stmt = static::prepare("DELETE FROM {$tableName} WHERE {$primaryKeyName} = ?");
        
        return $stmt->execute([$this->{$primaryKeyName}]);
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
