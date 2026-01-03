<?php
/**
 * Subcategory Model
 */

require_once __DIR__ . '/../core/Model.php';

class SubcategoryModel extends Model
{
    protected $table = 'subcategories';

    public function getAll(): array
    {
        $sql = "SELECT s.*, c.name as category_name 
                FROM {$this->table} s 
                LEFT JOIN categories c ON s.category_id = c.id 
                ORDER BY c.name ASC, s.display_order ASC, s.name ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getPaginated(int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        // Get total count
        $countStmt = $this->pdo->query("SELECT COUNT(*) as total FROM {$this->table}");
        $total = (int)$countStmt->fetch()['total'];

        // Get paginated data
        $sql = "SELECT s.*, c.name as category_name 
                FROM {$this->table} s 
                LEFT JOIN categories c ON s.category_id = c.id 
                ORDER BY c.name ASC, s.display_order ASC, s.name ASC
                LIMIT :limit OFFSET :offset";
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

    public function getByCategoryId(int $categoryId): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE category_id = :category_id 
                ORDER BY display_order ASC, name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':category_id' => $categoryId]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT s.*, c.name as category_name 
                FROM {$this->table} s 
                LEFT JOIN categories c ON s.category_id = c.id 
                WHERE s.id = :id 
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }

    public function createSubcategory(array $data): int
    {
        return $this->create($data);
    }

    public function updateSubcategory(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function deleteSubcategory(int $id): bool
    {
        return $this->delete($id);
    }

    public function slugExists(string $slug, int $categoryId, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE slug = :slug AND category_id = :category_id";
        $params = [
            ':slug' => $slug,
            ':category_id' => $categoryId
        ];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }
}

