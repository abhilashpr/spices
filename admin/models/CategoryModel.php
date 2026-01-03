<?php
/**
 * Category Model
 */

require_once __DIR__ . '/../core/Model.php';

class CategoryModel extends Model
{
    protected $table = 'categories';

    public function getAll(): array
    {
        return $this->findAll('display_order ASC, name ASC');
    }

    public function getPaginated(int $page = 1, int $perPage = 10): array
    {
        return $this->findPaginated($page, $perPage, 'display_order ASC, name ASC');
    }

    public function getById(int $id): ?array
    {
        return $this->findById($id);
    }

    public function createCategory(array $data): int
    {
        return $this->create($data);
    }

    public function updateCategory(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->delete($id);
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = :slug";
        $params = [':slug' => $slug];

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

