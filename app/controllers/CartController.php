<?php
/**
 * Cart Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../helpers/helpers.php';

class CartController extends Controller
{
    private $cartModel;
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
    }

    public function index(): void
    {
        if (is_logged_in()) {
            // Get cart from database for logged-in user
            $user = get_logged_in_user();
            $userId = $user['id'] ?? null;
            
            if (!$userId) {
                error_log("CartController::index - User ID not found for logged-in user");
                $cart = [];
            } else {
                // Always fetch fresh cart from database (ignore any session cart)
                error_log("CartController::index - Fetching cart from database for user ID: $userId");
                $cart = $this->cartModel->getUserCart($userId);
                error_log("CartController::index - Found " . count($cart) . " items in database cart");
            }
        } else {
            // Get cart from session and enhance with product details
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            $cart = [];
            foreach ($_SESSION['cart'] as $key => $item) {
                $product = $this->productModel->getById($item['product_id']);
                if ($product) {
                    $sku = $this->productModel->getProductSKUs($item['product_id']);
                    $selectedSku = null;
                    foreach ($sku as $s) {
                        if ($s['id'] == $item['sku_id']) {
                            $selectedSku = $s;
                            break;
                        }
                    }
                    if ($selectedSku) {
                        $cart[] = [
                            'cart_item_id' => $key,
                            'product_id' => $product['id'],
                            'product_name' => $product['name'],
                            'product_slug' => $product['slug'],
                            'main_image' => $product['main_image'] ?? null,
                            'sku_id' => $selectedSku['id'],
                            'sku_value' => $selectedSku['value'],
                            'unit_symbol' => $selectedSku['unit_symbol'] ?? '',
                            'price' => $selectedSku['price'],
                            'offer_price' => $selectedSku['offer_price'] ?? null,
                            'quantity' => $item['quantity'],
                            'unit' => $item['unit'] ?? null
                        ];
                    }
                }
            }
        }

        $this->render('cart/index', [
            'layout' => 'main',
            'pageTitle' => 'Shopping Cart | Wynvalley',
            'cart' => $cart
        ]);
    }

    public function add(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $productId = (int)($input['product_id'] ?? $_POST['product_id'] ?? 0);
        $skuId = (int)($input['sku_id'] ?? $_POST['sku_id'] ?? 0);
        $quantity = (int)($input['quantity'] ?? $_POST['quantity'] ?? 1);
        $unit = $input['unit'] ?? $_POST['unit'] ?? null;

        if ($productId <= 0 || $skuId <= 0 || $quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product, SKU, or quantity']);
            exit;
        }

        if (is_logged_in()) {
            // Add to database cart
            $user = get_logged_in_user();
            $userId = $user['id'] ?? null;

            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'User not found']);
                exit;
            }

            $success = $this->cartModel->addToCart($userId, $productId, $skuId, $unit, $quantity);

            if ($success) {
                $cartCount = $this->cartModel->getCartCount($userId);
                echo json_encode([
                    'success' => true, 
                    'message' => 'Item added to cart successfully!',
                    'cart_count' => $cartCount
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add item to cart']);
            }
        } else {
            // Add to session cart
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Add or update cart item
            $cartKey = $productId . '_' . $skuId;
            $isNewItem = !isset($_SESSION['cart'][$cartKey]);
            
            if ($isNewItem) {
                // Add new item
                $_SESSION['cart'][$cartKey] = [
                    'product_id' => $productId,
                    'sku_id' => $skuId,
                    'unit' => $unit,
                    'quantity' => $quantity
                ];
                $message = 'Item added to cart successfully!';
            } else {
                // Update existing item quantity
                $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
                $message = 'Cart quantity updated successfully!';
            }

            // Calculate cart count from session
            $cartCount = 0;
            foreach ($_SESSION['cart'] as $item) {
                $cartCount += (int)($item['quantity'] ?? 0);
            }

            echo json_encode([
                'success' => true, 
                'message' => $message,
                'cart_count' => $cartCount
            ]);
        }
    }

    public function remove(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $cartItemId = $input['cart_item_id'] ?? $_POST['cart_item_id'] ?? null;

        if (is_logged_in()) {
            // For logged-in users, cart_item_id is an integer (database ID)
            $cartItemId = (int)$cartItemId;
            
            if ($cartItemId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid cart item ID']);
                exit;
            }

            $user = get_logged_in_user();
            $userId = $user['id'] ?? null;

            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'User not found']);
                exit;
            }

            $success = $this->cartModel->removeFromCart($cartItemId, $userId);

            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Item removed from cart successfully!'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove item from cart']);
            }
        } else {
            // For session cart, cart_item_id is a string key like "productId_skuId"
            if (empty($cartItemId)) {
                echo json_encode(['success' => false, 'message' => 'Invalid cart item ID']);
                exit;
            }

            // Ensure session cart exists
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Remove from session cart using the string key
            if (isset($_SESSION['cart'][$cartItemId])) {
                unset($_SESSION['cart'][$cartItemId]);
                echo json_encode([
                    'success' => true,
                    'message' => 'Item removed from cart successfully!'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
            }
        }
    }
}

