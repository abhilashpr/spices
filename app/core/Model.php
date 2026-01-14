<?php
/**
 * Base Model Class for Frontend
 */

abstract class Model
{
    protected $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../config/database.php';
        try {
            $this->pdo = get_db_connection();
        } catch (Exception $e) {
            error_log("Model database connection error: " . $e->getMessage());
            error_log("Model stack trace: " . $e->getTraceAsString());
            throw new Exception("Database connection failed: " . $e->getMessage(), 0, $e);
        } catch (Error $e) {
            error_log("Model fatal error: " . $e->getMessage());
            error_log("Model stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Find record by ID in a specific table
     * @deprecated Use findOneBy() instead
     */
    protected function findByIdInTable(int $id, string $table): ?array
    {
        return $this->findOneBy($table, 'id', $id);
    }

    protected function findAll(string $table, string $orderBy = 'id DESC'): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$table} ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    protected function findBy(string $table, string $field, $value, string $orderBy = 'id DESC'): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE {$field} = :value ORDER BY {$orderBy}");
        $stmt->execute([':value' => $value]);
        return $stmt->fetchAll();
    }

    protected function findOneBy(string $table, string $field, $value): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE {$field} = :value LIMIT 1");
        $stmt->execute([':value' => $value]);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }
}

