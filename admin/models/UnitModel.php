<?php
/**
 * Unit Model
 */

require_once __DIR__ . '/../core/Model.php';

class UnitModel extends Model
{
    protected $table = 'units';

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

    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY display_order ASC, name ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function createUnit(array $data): int
    {
        // Ensure boolean field
        if (isset($data['is_active'])) {
            $data['is_active'] = (int)(bool)$data['is_active'];
        }
        return $this->create($data);
    }

    public function updateUnit(int $id, array $data): bool
    {
        // Ensure boolean field
        if (isset($data['is_active'])) {
            $data['is_active'] = (int)(bool)$data['is_active'];
        }
        return $this->update($id, $data);
    }

    public function deleteUnit(int $id): bool
    {
        return $this->delete($id);
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = :name";
        $params = [':name' => $name];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }

    public function symbolExists(string $symbol, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE symbol = :symbol";
        $params = [':symbol' => $symbol];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }

    public function toggleActive(int $id): bool
    {
        $unit = $this->getById($id);
        if (!$unit) {
            return false;
        }

        $newStatus = $unit['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }
}

