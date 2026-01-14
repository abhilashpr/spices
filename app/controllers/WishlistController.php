<?php
/**
 * Wishlist Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/WishlistModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../helpers/helpers.php';

class WishlistController extends Controller
{
    private $wishlistModel;
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->wishlistModel = new WishlistModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Display wishlist page or handle API actions
     */
    public function index(): void
    {
        // Check for action parameter (toggle, check)
        $action = $_GET['action'] ?? null;
        
        if ($action === 'toggle') {
            $this->toggle();
            return;
        }
        
        if ($action === 'check') {
            $this->check();
            return;
        }

        // Check if user is logged in
        if (!is_logged_in()) {
            header('Location: /online-sp/login');
            exit;
        }

        $user = get_logged_in_user();
        $userId = $user['id'] ?? null;

        if (!$userId) {
            header('Location: /online-sp/login');
            exit;
        }

        // Get user's wishlist products (already includes product data from JOIN)
        $wishlistProducts = $this->wishlistModel->getUserWishlist($userId);

        // Get product IDs
        $productIds = array_column($wishlistProducts, 'id');
        
        // Get ratings for products
        $ratings = [];
        if (!empty($productIds)) {
            $ratings = $this->productModel->getProductsRatings($productIds);
        }

        // Enhance products with ratings and price info
        $products = [];
        foreach ($wishlistProducts as $product) {
            $productId = $product['id'];
            
            // Add rating data
            $product['rating'] = $ratings[$productId]['average'] ?? 0;
            $product['review_count'] = $ratings[$productId]['count'] ?? 0;
            
            // Get price info
            $priceInfo = $this->productModel->getPriceInfo($productId);
            $product['min_price'] = $priceInfo['price'];
            $product['min_offer_price'] = $priceInfo['offer_price'];
            
            $products[] = $product;
        }

        // Render wishlist view
        $this->render('wishlist/index', [
            'layout' => 'main',
            'pageTitle' => 'My Wishlist | Wynvalley',
            'products' => $products,
            'user' => $user
        ]);
    }

    /**
     * Toggle wishlist (add/remove)
     */
    public function toggle(): void
    {
        header('Content-Type: application/json');

        // Check if user is logged in
        if (!is_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Please login then only wishlist',
                'login_required' => true
            ]);
            exit;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode([
                'success' => false,
                'message' => 'User ID not found',
                'login_required' => true
            ]);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? $_POST['product_id'] ?? null;

        if (!$productId) {
            echo json_encode([
                'success' => false,
                'message' => 'Product ID is required'
            ]);
            exit;
        }

        $productId = (int) $productId;

        // Check if already in wishlist
        $isInWishlist = $this->wishlistModel->isInWishlist($userId, $productId);

        if ($isInWishlist) {
            // Remove from wishlist
            $success = $this->wishlistModel->removeFromWishlist($userId, $productId);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Removed from wishlist' : 'Failed to remove from wishlist',
                'in_wishlist' => false
            ]);
        } else {
            // Add to wishlist
            $success = $this->wishlistModel->addToWishlist($userId, $productId);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Added to wishlist' : 'Failed to add to wishlist',
                'in_wishlist' => true
            ]);
        }
    }

    /**
     * Check if product is in wishlist
     */
    public function check(): void
    {
        header('Content-Type: application/json');

        if (!is_logged_in()) {
            echo json_encode([
                'success' => false,
                'in_wishlist' => false,
                'login_required' => true
            ]);
            exit;
        }

        $userId = $_SESSION['user_id'] ?? null;
        $productId = $_GET['product_id'] ?? null;

        if (!$userId || !$productId) {
            echo json_encode([
                'success' => false,
                'in_wishlist' => false
            ]);
            exit;
        }

        $productId = (int) $productId;
        $isInWishlist = $this->wishlistModel->isInWishlist($userId, $productId);

        echo json_encode([
            'success' => true,
            'in_wishlist' => $isInWishlist
        ]);
    }
}
