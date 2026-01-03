<?php
/**
 * Product Model
 */

require_once __DIR__ . '/../core/Model.php';

class ProductModel extends Model
{
    protected $table = 'products';

    public function getAll(): array
    {
        $sql = "SELECT p.*, 
                COUNT(DISTINCT pi.id) as image_count,
                COUNT(DISTINCT pl.id) as language_count
                FROM {$this->table} p
                LEFT JOIN product_images pi ON p.id = pi.product_id
                LEFT JOIN product_languages pl ON p.id = pl.product_id
                GROUP BY p.id
                ORDER BY p.created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get paginated products
     * 
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getPaginated(int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        // Get total count (need to count distinct products)
        $countSql = "SELECT COUNT(DISTINCT p.id) as total 
                     FROM {$this->table} p";
        $countStmt = $this->pdo->query($countSql);
        $total = (int)$countStmt->fetch()['total'];

        // Get paginated data
        $sql = "SELECT p.*, 
                COUNT(DISTINCT pi.id) as image_count,
                COUNT(DISTINCT pl.id) as language_count,
                COUNT(DISTINCT psku.id) as sku_count
                FROM {$this->table} p
                LEFT JOIN product_images pi ON p.id = pi.product_id
                LEFT JOIN product_languages pl ON p.id = pl.product_id
                LEFT JOIN product_skus psku ON p.id = psku.product_id
                GROUP BY p.id
                ORDER BY p.created_at DESC
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

    public function getById(int $id): ?array
    {
        $product = $this->findById($id);
        if (!$product) {
            return null;
        }

        // Get product languages
        $sql = "SELECT * FROM product_languages WHERE product_id = :product_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $id]);
        $product['languages'] = $stmt->fetchAll();

        // Get product images
        $sql = "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY display_order ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $id]);
        $product['images'] = $stmt->fetchAll();

        return $product;
    }

    public function createProduct(array $data): int
    {
        // Ensure boolean fields
        if (isset($data['is_active'])) {
            $data['is_active'] = (int)(bool)$data['is_active'];
        }
        if (isset($data['is_out_of_stock'])) {
            $data['is_out_of_stock'] = (int)(bool)$data['is_out_of_stock'];
        }

        // Check which columns actually exist in the table
        $existingColumns = $this->getTableColumns();
        
        // Filter data to only include columns that exist
        $filteredData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $existingColumns)) {
                $filteredData[$key] = $value;
            }
        }

        return $this->create($filteredData);
    }
    
    /**
     * Get list of columns in the products table
     */
    private function getTableColumns(): array
    {
        $sql = "SHOW COLUMNS FROM {$this->table}";
        $stmt = $this->pdo->query($sql);
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $columns;
    }

    public function updateProduct(int $id, array $data): bool
    {
        // Ensure boolean fields
        if (isset($data['is_active'])) {
            $data['is_active'] = (int)(bool)$data['is_active'];
        }
        if (isset($data['is_out_of_stock'])) {
            $data['is_out_of_stock'] = (int)(bool)$data['is_out_of_stock'];
        }

        return $this->update($id, $data);
    }

    public function deleteProduct(int $id): bool
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

    public function productCodeExists(string $productCode, ?int $excludeId = null): bool
    {
        if (empty($productCode)) {
            return false;
        }

        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE product_code = :product_code";
        $params = [':product_code' => $productCode];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }

    /**
     * Save product languages
     */
    public function saveProductLanguages(int $productId, array $languages): void
    {
        // Delete existing languages
        $sql = "DELETE FROM product_languages WHERE product_id = :product_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);

        // Insert new languages
        if (!empty($languages)) {
            $sql = "INSERT INTO product_languages (product_id, language_code, product_name) VALUES (:product_id, :language_code, :product_name)";
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($languages as $lang) {
                if (!empty($lang['code']) && !empty($lang['name'])) {
                    $stmt->execute([
                        ':product_id' => $productId,
                        ':language_code' => trim($lang['code']),
                        ':product_name' => trim($lang['name'])
                    ]);
                }
            }
        }
    }

    /**
     * Save product images (additional images)
     */
    public function saveProductImages(int $productId, array $imagePaths): void
    {
        // Delete existing images
        $sql = "DELETE FROM product_images WHERE product_id = :product_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);

        // Insert new images
        if (!empty($imagePaths)) {
            $sql = "INSERT INTO product_images (product_id, image_path, display_order) VALUES (:product_id, :image_path, :display_order)";
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($imagePaths as $index => $imagePath) {
                if (!empty($imagePath)) {
                    $stmt->execute([
                        ':product_id' => $productId,
                        ':image_path' => $imagePath,
                        ':display_order' => $index
                    ]);
                }
            }
        }
    }

    /**
     * Get product images
     */
    public function getProductImages(int $productId): array
    {
        $sql = "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY display_order ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll();
    }
}

