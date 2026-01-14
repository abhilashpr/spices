<?php
/**
 * Product Model
 */

require_once __DIR__ . '/../core/Model.php';

class ProductModel extends Model
{
    public function getAll(): array
    {
        try {
            // Check if is_active column exists
            $checkColumn = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'is_active'");
            $hasActiveColumn = $checkColumn->rowCount() > 0;
            
            if ($hasActiveColumn) {
                $stmt = $this->pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY name");
            } else {
                $stmt = $this->pdo->query("SELECT * FROM products ORDER BY name");
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting all products: " . $e->getMessage());
            // Fallback to simple query
            try {
                $stmt = $this->pdo->query("SELECT * FROM products ORDER BY name");
                return $stmt->fetchAll();
            } catch (Exception $e2) {
                error_log("Error in fallback query: " . $e2->getMessage());
                return [];
            }
        }
    }

    public function getBestSellers(int $limit = 3): array
    {
        try {
            // Check if is_active column exists
            $checkColumn = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'is_active'");
            $hasActiveColumn = $checkColumn->rowCount() > 0;
            
            if ($hasActiveColumn) {
                $sql = "SELECT * FROM products WHERE best_seller = 1 AND is_active = 1 ORDER BY best_seller_order LIMIT :limit";
            } else {
                $sql = "SELECT * FROM products WHERE best_seller = 1 ORDER BY best_seller_order LIMIT :limit";
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting best sellers: " . $e->getMessage());
            return [];
        }
    }

    public function getCollectionProducts(int $limit = 3): array
    {
        try {
            // Check if is_active column exists
            $checkColumn = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'is_active'");
            $hasActiveColumn = $checkColumn->rowCount() > 0;
            
            if ($hasActiveColumn) {
                $sql = "SELECT * FROM products WHERE collection = 1 AND is_active = 1 ORDER BY collection_order LIMIT :limit";
            } else {
                $sql = "SELECT * FROM products WHERE collection = 1 ORDER BY collection_order LIMIT :limit";
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting collection products: " . $e->getMessage());
            return [];
        }
    }

    public function getBySlug(string $slug): ?array
    {
        return $this->findOneBy('products', 'slug', $slug);
    }

    public function getById(int $id): ?array
    {
        return $this->findOneBy('products', 'id', $id);
    }

    public function getProductMedia(int $productId): array
    {
        $sql = "SELECT * FROM product_media WHERE product_id = :product_id ORDER BY display_order";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function getProductImages(int $productId): array
    {
        $sql = "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY display_order";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function getRelatedProducts(int $productId, int $limit = 4): array
    {
        try {
            // Check if is_active column exists
            $checkColumn = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'is_active'");
            $hasActiveColumn = $checkColumn->rowCount() > 0;
            
            if ($hasActiveColumn) {
                $sql = "SELECT p.* FROM products p 
                        INNER JOIN related_products rp ON p.id = rp.related_product_id 
                        WHERE rp.product_id = :product_id AND p.is_active = 1 
                        LIMIT :limit";
            } else {
                $sql = "SELECT p.* FROM products p 
                        INNER JOIN related_products rp ON p.id = rp.related_product_id 
                        WHERE rp.product_id = :product_id 
                        LIMIT :limit";
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting related products: " . $e->getMessage());
            return [];
        }
    }

    public function getProductReviews(int $productId, int $limit = 20): array
    {
        $sql = "SELECT * FROM product_reviews WHERE product_id = :product_id ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get average rating and review count for a product
     */
    public function getProductRating(int $productId): array
    {
        try {
            $sql = "SELECT 
                    AVG(rating) as average_rating,
                    COUNT(*) as review_count
                    FROM product_reviews 
                    WHERE product_id = :product_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':product_id' => $productId]);
            $result = $stmt->fetch();
            
            return [
                'average' => $result ? round((float)$result['average_rating'], 1) : 0,
                'count' => $result ? (int)$result['review_count'] : 0
            ];
        } catch (Exception $e) {
            error_log("Error getting product rating: " . $e->getMessage());
            return ['average' => 0, 'count' => 0];
        }
    }

    /**
     * Get average ratings for multiple products (for category listings)
     */
    public function getProductsRatings(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }
        
        try {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $sanitizedIds = array_map('intval', $productIds);
            
            $sql = "SELECT 
                    product_id,
                    AVG(rating) as average_rating,
                    COUNT(*) as review_count
                    FROM product_reviews 
                    WHERE product_id IN ($placeholders)
                    GROUP BY product_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($sanitizedIds);
            $results = $stmt->fetchAll();
            
            $ratings = [];
            foreach ($results as $result) {
                $ratings[$result['product_id']] = [
                    'average' => round((float)$result['average_rating'], 1),
                    'count' => (int)$result['review_count']
                ];
            }
            
            return $ratings;
        } catch (Exception $e) {
            error_log("Error getting products ratings: " . $e->getMessage());
            return [];
        }
    }

    public function getProductLanguages(int $productId): array
    {
        $sql = "SELECT * FROM product_languages WHERE product_id = :product_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function getProductSKUs(int $productId): array
    {
        $sql = "SELECT psku.*, u.name as unit_name, u.symbol as unit_symbol 
                FROM product_skus psku 
                LEFT JOIN units u ON psku.unit_id = u.id 
                WHERE psku.product_id = :product_id AND psku.is_in_stock = 1 
                ORDER BY psku.price ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get products by subcategory ID (group_id) - only active products
     */
    public function getBySubcategory(int $subcategoryId): array
    {
        try {
            // Check if is_active column exists
            $checkColumn = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'is_active'");
            $hasActiveColumn = $checkColumn->rowCount() > 0;
            
            // Check if group_id column exists (for subcategory relationship)
            $checkGroupId = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'group_id'");
            $hasGroupId = $checkGroupId->rowCount() > 0;
            
            if ($hasGroupId) {
                // Always filter by group_id and is_active = 1
                $sql = "SELECT * FROM products WHERE group_id = :subcategory_id";
                if ($hasActiveColumn) {
                    $sql .= " AND is_active = 1";
                }
                $sql .= " ORDER BY name";
            } else {
                // Fallback: try subcategory_id
                $checkSubcategoryId = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'subcategory_id'");
                $hasSubcategoryId = $checkSubcategoryId->rowCount() > 0;
                
                if ($hasSubcategoryId) {
                    $sql = "SELECT * FROM products WHERE subcategory_id = :subcategory_id";
                    if ($hasActiveColumn) {
                        $sql .= " AND is_active = 1";
                    }
                    $sql .= " ORDER BY name";
                } else {
                    // If no relationship column exists, return empty
                    return [];
                }
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':subcategory_id' => $subcategoryId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting products by subcategory: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get products by multiple subcategory IDs (for multi-select filtering)
     */
    public function getBySubcategories(array $subcategoryIds): array
    {
        if (empty($subcategoryIds)) {
            return [];
        }
        
        try {
            // Check if is_active column exists
            $checkColumn = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'is_active'");
            $hasActiveColumn = $checkColumn->rowCount() > 0;
            
            // Check if group_id column exists (for subcategory relationship)
            $checkGroupId = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'group_id'");
            $hasGroupId = $checkGroupId->rowCount() > 0;
            
            // Sanitize subcategory IDs to ensure they're integers
            $subcategoryIds = array_map('intval', $subcategoryIds);
            $subcategoryIds = array_filter($subcategoryIds, function($id) {
                return $id > 0;
            });
            
            if (empty($subcategoryIds)) {
                return [];
            }
            
            // Create placeholders for IN clause
            $placeholders = implode(',', array_fill(0, count($subcategoryIds), '?'));
            
            if ($hasGroupId) {
                // Filter by group_id IN (multiple values) and is_active = 1
                $sql = "SELECT * FROM products WHERE group_id IN ($placeholders)";
                if ($hasActiveColumn) {
                    $sql .= " AND is_active = 1";
                }
                $sql .= " ORDER BY name";
            } else {
                // Fallback: try subcategory_id
                $checkSubcategoryId = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'subcategory_id'");
                $hasSubcategoryId = $checkSubcategoryId->rowCount() > 0;
                
                if ($hasSubcategoryId) {
                    $sql = "SELECT * FROM products WHERE subcategory_id IN ($placeholders)";
                    if ($hasActiveColumn) {
                        $sql .= " AND is_active = 1";
                    }
                    $sql .= " ORDER BY name";
                } else {
                    // If no relationship column exists, return empty
                    return [];
                }
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($subcategoryIds);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting products by subcategories: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get products by category ID
     */
    public function getByCategory(int $categoryId): array
    {
        try {
            // Check if is_active column exists
            $checkColumn = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'is_active'");
            $hasActiveColumn = $checkColumn->rowCount() > 0;
            
            // Check if category_id column exists
            $checkCategoryId = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'category_id'");
            $hasCategoryId = $checkCategoryId->rowCount() > 0;
            
            if ($hasCategoryId) {
                $sql = "SELECT * FROM products WHERE category_id = :category_id";
                if ($hasActiveColumn) {
                    $sql .= " AND is_active = 1";
                }
                $sql .= " ORDER BY name";
            } else {
                // If category_id doesn't exist, return empty array
                return [];
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':category_id' => $categoryId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting products by category: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get minimum price for a product (from SKUs)
     */
    public function getMinPrice(int $productId): ?float
    {
        try {
            $sql = "SELECT MIN(price) as min_price FROM product_skus WHERE product_id = :product_id AND is_in_stock = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':product_id' => $productId]);
            $result = $stmt->fetch();
            return $result && $result['min_price'] !== null ? (float)$result['min_price'] : null;
        } catch (Exception $e) {
            error_log("Error getting min price: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get price info for a product (min price and offer price)
     * Returns array with 'price' and 'offer_price' keys
     */
    public function getPriceInfo(int $productId): array
    {
        try {
            $sql = "SELECT 
                        MIN(price) as min_price,
                        MIN(CASE WHEN offer_price > 0 AND offer_price < price THEN offer_price ELSE NULL END) as min_offer_price
                    FROM product_skus 
                    WHERE product_id = :product_id AND is_in_stock = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':product_id' => $productId]);
            $result = $stmt->fetch();
            
            $price = $result && $result['min_price'] !== null ? (float)$result['min_price'] : null;
            $offerPrice = $result && $result['min_offer_price'] !== null ? (float)$result['min_offer_price'] : null;
            
            return [
                'price' => $price,
                'offer_price' => $offerPrice
            ];
        } catch (Exception $e) {
            error_log("Error getting price info: " . $e->getMessage());
            return ['price' => null, 'offer_price' => null];
        }
    }
}

