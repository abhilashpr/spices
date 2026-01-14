<?php
/**
 * Cart Model
 */

require_once __DIR__ . '/../config/database.php';

class CartModel
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = get_db_connection();
    }

    /**
     * Add item to cart
     */
    public function addToCart(int $userId, int $productId, int $skuId, string $unit = null, int $quantity = 1): bool
    {
        try {
            // Check if item already exists in cart
            $stmt = $this->pdo->prepare("
                SELECT id, quantity 
                FROM cart 
                WHERE user_id = :user_id AND product_id = :product_id AND sku_id = :sku_id
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId,
                ':sku_id' => $skuId
            ]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Update quantity
                $newQuantity = $existing['quantity'] + $quantity;
                $stmt = $this->pdo->prepare("
                    UPDATE cart 
                    SET quantity = :quantity, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = :id
                ");
                return $stmt->execute([
                    ':quantity' => $newQuantity,
                    ':id' => $existing['id']
                ]);
            } else {
                // Insert new item
                $stmt = $this->pdo->prepare("
                    INSERT INTO cart (user_id, product_id, sku_id, unit, quantity) 
                    VALUES (:user_id, :product_id, :sku_id, :unit, :quantity)
                ");
                return $stmt->execute([
                    ':user_id' => $userId,
                    ':product_id' => $productId,
                    ':sku_id' => $skuId,
                    ':unit' => $unit,
                    ':quantity' => $quantity
                ]);
            }
        } catch (PDOException $e) {
            error_log("CartModel::addToCart error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's cart items
     */
    public function getUserCart(int $userId): array
    {
        try {
            error_log("CartModel::getUserCart - Fetching cart for user ID: $userId");
            $stmt = $this->pdo->prepare("
                SELECT c.id as cart_item_id, c.quantity, c.unit,
                       p.id as product_id, p.name as product_name, p.slug as product_slug, p.main_image,
                       ps.id as sku_id, ps.value as sku_value, u.symbol as unit_symbol, ps.price, ps.offer_price
                FROM cart c
                INNER JOIN products p ON c.product_id = p.id
                INNER JOIN product_skus ps ON c.sku_id = ps.id
                LEFT JOIN units u ON ps.unit_id = u.id
                WHERE c.user_id = :user_id
                ORDER BY c.created_at DESC
            ");
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("CartModel::getUserCart - Found " . count($result) . " items in cart");
            if (count($result) > 0) {
                error_log("CartModel::getUserCart - First item: " . print_r($result[0], true));
            }
            return $result;
        } catch (PDOException $e) {
            error_log("CartModel::getUserCart error: " . $e->getMessage());
            error_log("CartModel::getUserCart stack trace: " . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Migrate session cart to database cart
     * If item already exists in database cart, updates the quantity
     * If item doesn't exist, adds it as new item
     */
    public function migrateSessionCart(int $userId, array $sessionCart): bool
    {
        try {
            $migratedCount = 0;
            foreach ($sessionCart as $key => $item) {
                // Session cart uses keys like "productId_skuId" and values are arrays
                $productId = (int)($item['product_id'] ?? 0);
                $skuId = (int)($item['sku_id'] ?? 0);
                $quantity = (int)($item['quantity'] ?? 1);
                $unit = $item['unit'] ?? null;

                if ($productId > 0 && $skuId > 0 && $quantity > 0) {
                    // Use addToCart which automatically:
                    // - Checks if item exists (same user_id, product_id, sku_id)
                    // - If exists: adds quantity to existing quantity
                    // - If not exists: inserts new item
                    $result = $this->addToCart($userId, $productId, $skuId, $unit, $quantity);
                    if ($result) {
                        $migratedCount++;
                    }
                }
            }
            return $migratedCount > 0 || count($sessionCart) === 0; // Return true if migrated or empty cart
        } catch (PDOException $e) {
            error_log("CartModel::migrateSessionCart error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(int $cartId, int $quantity): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE cart 
                SET quantity = :quantity, updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id
            ");
            return $stmt->execute([
                ':quantity' => $quantity,
                ':id' => $cartId
            ]);
        } catch (PDOException $e) {
            error_log("CartModel::updateQuantity error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(int $cartId, int $userId): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM cart 
                WHERE id = :id AND user_id = :user_id
            ");
            return $stmt->execute([
                ':id' => $cartId,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("CartModel::removeFromCart error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear user's cart
     */
    public function clearCart(int $userId): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
            return $stmt->execute([':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("CartModel::clearCart error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cart item count for a user
     */
    public function getCartCount(int $userId): int
    {
        try {
            $stmt = $this->pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("CartModel::getCartCount error: " . $e->getMessage());
            return 0;
        }
    }
}

