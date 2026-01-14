<?php
/**
 * Wishlist Model
 */

require_once __DIR__ . '/../core/Model.php';

class WishlistModel extends Model
{
    /**
     * Add product to wishlist
     */
    public function addToWishlist(int $userId, int $productId): bool
    {
        try {
            $sql = "INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)
                    ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Error adding to wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove product from wishlist
     */
    public function removeFromWishlist(int $userId, int $productId): bool
    {
        try {
            $sql = "DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Error removing from wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if product is in user's wishlist
     */
    public function isInWishlist(int $userId, int $productId): bool
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log("Error checking wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's wishlist products
     */
    public function getUserWishlist(int $userId): array
    {
        try {
            $sql = "SELECT p.* FROM products p 
                    INNER JOIN wishlist w ON p.id = w.product_id 
                    WHERE w.user_id = :user_id 
                    ORDER BY w.created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting user wishlist: " . $e->getMessage());
            return [];
        }
    }
}

