<?php
/**
 * Product SKU Model
 */

require_once __DIR__ . '/../core/Model.php';

class ProductSKUModel extends Model
{
    protected $table = 'product_skus';

    /**
     * Get all SKUs for a product
     * 
     * @param int $productId
     * @return array
     */
    public function getByProductId(int $productId): array
    {
        try {
            $sql = "SELECT sku.*, u.name as unit_name, u.symbol as unit_symbol
                    FROM {$this->table} sku
                    LEFT JOIN units u ON sku.unit_id = u.id
                    WHERE sku.product_id = :product_id
                    ORDER BY sku.created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':product_id' => $productId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get SKU by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT sku.*, u.name as unit_name, u.symbol as unit_symbol
                FROM {$this->table} sku
                JOIN units u ON sku.unit_id = u.id
                WHERE sku.id = :id
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }

    /**
     * Create a new SKU
     * 
     * @param array $data
     * @return int
     */
    public function createSKU(array $data): int
    {
        // Ensure boolean field
        if (isset($data['is_in_stock'])) {
            $data['is_in_stock'] = (int)(bool)$data['is_in_stock'];
        }

        return $this->create($data);
    }

    /**
     * Update an existing SKU
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateSKU(int $id, array $data): bool
    {
        // Ensure boolean field
        if (isset($data['is_in_stock'])) {
            $data['is_in_stock'] = (int)(bool)$data['is_in_stock'];
        }

        return $this->update($id, $data);
    }

    /**
     * Delete a SKU
     * 
     * @param int $id
     * @return bool
     */
    public function deleteSKU(int $id): bool
    {
        return $this->delete($id);
    }
}

