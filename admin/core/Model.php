<?php
/**
 * Base Model Class
 */

abstract class Model
{
    protected $pdo;
    protected $table;

    public function __construct()
    {
        $this->pdo = get_db_connection();
    }

    protected function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }

    protected function findAll(string $orderBy = 'id DESC'): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    /**
     * Get paginated results
     * 
     * @param int $page Current page number (1-based)
     * @param int $perPage Number of items per page
     * @param string $orderBy ORDER BY clause
     * @return array ['data' => array, 'total' => int, 'pages' => int, 'current_page' => int, 'per_page' => int]
     */
    protected function findPaginated(int $page = 1, int $perPage = 10, string $orderBy = 'id DESC'): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        // Get total count
        $countStmt = $this->pdo->query("SELECT COUNT(*) as total FROM {$this->table}");
        $total = (int)$countStmt->fetch()['total'];

        // Get paginated data
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        $pages = (int)ceil($total / $perPage);

        return [
            'data' => $data,
            'total' => $total,
            'pages' => $pages,
            'current_page' => $page,
            'per_page' => $perPage
        ];
    }

    protected function create(array $data): int
    {
        if (empty($data)) {
            throw new Exception("No data provided for insert");
        }
        
        // Get table columns to check which fields are required
        $stmt = $this->pdo->query("SHOW COLUMNS FROM {$this->table}");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $columnInfo = [];
        foreach ($columns as $col) {
            $columnInfo[$col['Field']] = [
                'null' => $col['Null'] === 'YES',
                'default' => $col['Default'],
                'type' => $col['Type'],
                'extra' => $col['Extra'] ?? ''
            ];
        }
        
        // Filter data - only include existing columns and exclude auto-increment columns (like id)
        $filteredData = [];
        foreach ($data as $key => $value) {
            // Always exclude id column (auto-increment)
            if ($key === 'id') {
                continue;
            }
            
            if (isset($columnInfo[$key])) {
                // Skip auto-increment columns
                if (strpos($columnInfo[$key]['extra'], 'auto_increment') !== false) {
                    continue;
                }
                
                // Handle empty strings for different field types
                if ($value === '' || $value === null) {
                    // For nullable columns, set to NULL
                    if ($columnInfo[$key]['null']) {
                        $filteredData[$key] = null;
                    } 
                    // For NOT NULL columns with defaults, use the default
                    elseif ($columnInfo[$key]['default'] !== null) {
                        $filteredData[$key] = $columnInfo[$key]['default'];
                    }
                    // For integer/float types, don't include empty strings - use NULL or default
                    elseif (preg_match('/^(int|tinyint|smallint|mediumint|bigint|decimal|float|double)/i', $columnInfo[$key]['type'])) {
                        // For integer fields without defaults, use 0 or NULL based on nullability
                        if ($columnInfo[$key]['null']) {
                            $filteredData[$key] = null;
                        } else {
                            $filteredData[$key] = 0;
                        }
                    }
                    // For other required fields, skip them (they should have defaults or be provided)
                    else {
                        // Skip empty strings for required fields - they should have defaults
                        continue;
                    }
                } else {
                    $filteredData[$key] = $value;
                }
            }
        }
        
        // Check for required NOT NULL columns without defaults that are missing
        foreach ($columnInfo as $colName => $colInfo) {
            // Skip id and auto-increment columns
            if ($colName === 'id' || strpos($colInfo['extra'], 'auto_increment') !== false) {
                continue;
            }
            
            if ($colInfo['null'] === false && $colInfo['default'] === null && !isset($filteredData[$colName])) {
                // This is a required field - provide a default value based on field name and type
                if (preg_match('/^(int|tinyint|smallint|mediumint|bigint)/i', $colInfo['type'])) {
                    // Integer fields default to 0
                    $filteredData[$colName] = 0;
                } elseif (preg_match('/^(decimal|float|double)/i', $colInfo['type'])) {
                    // Decimal/float fields default to 0.0
                    $filteredData[$colName] = 0.0;
                } elseif (strpos($colName, 'image') !== false || strpos($colName, 'image_class') !== false) {
                    $filteredData[$colName] = 'default';
                } elseif (strpos($colName, 'region') !== false) {
                    $filteredData[$colName] = 'general';
                } elseif (strpos($colName, 'craft') !== false) {
                    $filteredData[$colName] = 'blended';
                } elseif (strpos($colName, 'heat') !== false) {
                    $filteredData[$colName] = 'mild';
                } elseif (strpos($colName, 'summary') !== false || strpos($colName, 'description') !== false) {
                    // Try to get name or slug as fallback
                    $filteredData[$colName] = $filteredData['name'] ?? $filteredData['slug'] ?? '';
                } else {
                    // Default to empty string for other text fields
                    $filteredData[$colName] = '';
                }
            }
        }
        
        if (empty($filteredData)) {
            throw new Exception("No valid data provided for insert");
        }
        
        $fields = array_keys($filteredData);
        $placeholders = ':' . implode(', :', $fields);
        $fieldsList = implode(', ', $fields);

        $sql = "INSERT INTO {$this->table} ({$fieldsList}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($filteredData);

        return (int)$this->pdo->lastInsertId();
    }

    protected function update(int $id, array $data): bool
    {
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "{$field} = :{$field}";
        }
        $setClause = implode(', ', $fields);

        $data['id'] = $id;
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($data);
    }

    protected function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}

