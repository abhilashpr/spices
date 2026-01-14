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
        try {
            // Check if is_active column exists
            $checkColumn = $this->pdo->query("SHOW COLUMNS FROM categories LIKE 'is_active'");
            $hasActiveColumn = $checkColumn->rowCount() > 0;
            
            if ($hasActiveColumn) {
                $stmt = $this->pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order, name");
            } else {
                $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY display_order, name");
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting categories: " . $e->getMessage());
            // Fallback to simple query
            try {
                $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY display_order, name");
                return $stmt->fetchAll();
            } catch (Exception $e2) {
                error_log("Error in fallback query: " . $e2->getMessage());
                return [];
            }
        }
    }

    public function getById(int $id): ?array
    {
        return $this->findOneBy('id', $id);
    }

    public function getSubcategories(int $categoryId): array
    {
        try {
            // Check if is_active column exists in subcategories table
            $checkColumn = $this->pdo->query("SHOW COLUMNS FROM subcategories LIKE 'is_active'");
            $hasActiveColumn = $checkColumn->rowCount() > 0;
            
            $sql = "SELECT * FROM subcategories WHERE category_id = :category_id";
            if ($hasActiveColumn) {
                $sql .= " AND is_active = 1";
            }
            $sql .= " ORDER BY display_order, name";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':category_id' => $categoryId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting subcategories: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get active categories with their active subcategories
     */
    public function getCategoriesWithSubcategories(): array
    {
        $categories = $this->getAll();
        $result = [];
        
        foreach ($categories as $category) {
            $subcategories = $this->getSubcategories($category['id']);
            if (!empty($subcategories)) {
                $category['subcategories'] = $subcategories;
                $result[] = $category;
            }
        }
        
        return $result;
    }
}

