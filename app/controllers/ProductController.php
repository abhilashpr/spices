<?php
/**
 * Product Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/WishlistModel.php';
require_once __DIR__ . '/../helpers/helpers.php';

class ProductController extends Controller
{
    private $productModel;
    private $wishlistModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel();
        $this->wishlistModel = new WishlistModel();
    }

    public function detail(): void
    {
        // Get slug from URL path or query string
        $requestUri = $_SERVER['REQUEST_URI'];
        $path = parse_url($requestUri, PHP_URL_PATH);
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        $path = trim($path, '/');
        $segments = $path ? explode('/', $path) : [];
        
        // Try to get slug from URL path (e.g., /product/product-slug)
        $slug = $segments[1] ?? $_GET['slug'] ?? null;

        if (!$slug) {
            $this->redirect(url('categories'));
            return;
        }

        $product = $this->productModel->getBySlug($slug);

        if (!$product) {
            http_response_code(404);
            $this->render('errors/404', [
                'layout' => 'main',
                'pageTitle' => 'Product not found | Wynvalley'
            ]);
            return;
        }

        $productId = (int)$product['id'];
        $productMedia = $this->productModel->getProductMedia($productId);
        $productImages = $this->productModel->getProductImages($productId);
        $relatedProducts = $this->productModel->getRelatedProducts($productId, 4);
        $productReviews = $this->productModel->getProductReviews($productId, 20);
        $productLanguages = $this->productModel->getProductLanguages($productId);
        $productSKUs = $this->productModel->getProductSKUs($productId);
        
        // Get SKU from URL parameter if available
        $selectedSkuId = isset($_GET['sku']) ? (int)$_GET['sku'] : null;
        
        // Reorder SKUs to put selected SKU first if found
        if ($selectedSkuId && !empty($productSKUs)) {
            $selectedSkuIndex = null;
            foreach ($productSKUs as $index => $sku) {
                if ((int)$sku['id'] === $selectedSkuId) {
                    $selectedSkuIndex = $index;
                    break;
                }
            }
            if ($selectedSkuIndex !== null) {
                $selectedSku = $productSKUs[$selectedSkuIndex];
                unset($productSKUs[$selectedSkuIndex]);
                array_unshift($productSKUs, $selectedSku);
                $productSKUs = array_values($productSKUs);
            }
        }

        // Get product benefits if table exists
        $benefitsByType = [
            'health_benefits' => [],
            'how_to_use' => [],
            'how_to_store' => []
        ];
        
        try {
            require_once __DIR__ . '/../config/database.php';
            $pdo = get_db_connection();
            $sql = "SELECT * FROM product_benefits WHERE product_id = :product_id ORDER BY benefit_type, id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':product_id' => $productId]);
            $benefits = $stmt->fetchAll();
            
            if (is_array($benefits)) {
                foreach ($benefits as $benefit) {
                    if (isset($benefit['benefit_type'])) {
                        $type = $benefit['benefit_type'];
                        if (isset($benefitsByType[$type])) {
                            $benefitsByType[$type][] = $benefit;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Table might not exist - ignore
        }

        // Get main image
        $mainImage = null;
        if (!empty($product['main_image'])) {
            $mainImage = get_image_url($product['main_image']);
        }

        // Format description paragraphs
        $descriptionParagraphs = [];
        $fullDescription = $product['full_description'] ?? '';
        if (!empty($fullDescription)) {
            $descriptionParagraphs = array_filter(array_map('trim', explode("\n\n", $fullDescription)));
        }

        // Build share URL with SKU if available
        $shareUrl = url('product?slug=' . urlencode($slug));
        if ($selectedSkuId) {
            $shareUrl .= '&sku=' . $selectedSkuId;
        }

        // Check if product is in wishlist (if user is logged in)
        $isInWishlist = false;
        if (is_logged_in()) {
            $user = get_logged_in_user();
            $userId = $user['id'] ?? null;
            if ($userId) {
                $isInWishlist = $this->wishlistModel->isInWishlist($userId, $productId);
            }
        }

        $this->render('product/detail', [
            'layout' => 'main',
            'pageTitle' => $product['name'] . ' | Wynvalley',
            'product' => $product,
            'mainImage' => $mainImage,
            'productMedia' => $productMedia,
            'productImages' => $productImages,
            'relatedProducts' => $relatedProducts,
            'productReviews' => $productReviews,
            'productLanguages' => $productLanguages,
            'productSKUs' => $productSKUs,
            'benefitsByType' => $benefitsByType,
            'descriptionParagraphs' => $descriptionParagraphs,
            'shareUrl' => $shareUrl,
            'isInWishlist' => $isInWishlist
        ]);
    }
}

