<?php
/**
 * Product SKU Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/ProductSKUModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/UnitModel.php';

class ProductSKUController extends Controller
{
    private $skuModel;
    private $productModel;
    private $unitModel;

    public function __construct()
    {
        parent::__construct();
        Auth::requireLogin();
        $this->skuModel = new ProductSKUModel();
        $this->productModel = new ProductModel();
        $this->unitModel = new UnitModel();
    }

    /**
     * Manage SKUs for a product
     */
    public function manage(): void
    {
        $productId = (int)($_GET['product_id'] ?? 0);

        if ($productId === 0) {
            $this->setFlash('Invalid product ID.', 'error');
            $this->redirect(admin_url('index.php?page=products'));
            return;
        }

        $product = $this->productModel->getById($productId);

        if (!$product) {
            $this->setFlash('Product not found.', 'error');
            $this->redirect(admin_url('index.php?page=products'));
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];
            
            if ($action === 'create') {
                $this->handleCreate($productId);
            } elseif ($action === 'delete') {
                $this->handleDelete($productId);
            }
        } else {
            $skus = $this->skuModel->getByProductId($productId);
            $units = $this->unitModel->getActive();
            $flash = $this->getFlash();

            $this->render('product_skus/manage', [
                'product' => $product,
                'skus' => $skus,
                'units' => $units,
                'flash' => $flash
            ]);
        }
    }

    /**
     * Handle SKU creation
     */
    private function handleCreate(int $productId): void
    {
        $price = !empty($_POST['price']) ? (float)$_POST['price'] : 0;
        $offerPrice = !empty($_POST['offer_price']) ? (float)$_POST['offer_price'] : null;
        $unitId = !empty($_POST['unit_id']) ? (int)$_POST['unit_id'] : 0;
        $value = !empty($_POST['value']) ? (float)$_POST['value'] : 1.00;
        $isInStock = isset($_POST['is_in_stock']) ? 1 : 0;

        // Validation
        if ($price <= 0) {
            $this->setFlash('Price must be greater than 0.', 'error');
            $this->redirect(admin_url('index.php?page=product_skus&action=manage&product_id=' . $productId));
            return;
        }

        if ($unitId === 0) {
            $this->setFlash('Please select a unit.', 'error');
            $this->redirect(admin_url('index.php?page=product_skus&action=manage&product_id=' . $productId));
            return;
        }

        if ($value <= 0) {
            $this->setFlash('Value must be greater than 0.', 'error');
            $this->redirect(admin_url('index.php?page=product_skus&action=manage&product_id=' . $productId));
            return;
        }

        if ($offerPrice !== null && $offerPrice >= $price) {
            $this->setFlash('Offer price must be less than regular price.', 'error');
            $this->redirect(admin_url('index.php?page=product_skus&action=manage&product_id=' . $productId));
            return;
        }

        try {
            $this->skuModel->createSKU([
                'product_id' => $productId,
                'price' => $price,
                'offer_price' => $offerPrice,
                'unit_id' => $unitId,
                'value' => $value,
                'is_in_stock' => $isInStock
            ]);
            $this->setFlash('SKU created successfully!', 'success');
        } catch (Exception $e) {
            $this->setFlash('Error creating SKU: ' . $e->getMessage(), 'error');
        }

        $this->redirect(admin_url('index.php?page=product_skus&action=manage&product_id=' . $productId));
    }

    /**
     * Handle SKU deletion
     */
    private function handleDelete(int $productId): void
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id === 0) {
            $this->setFlash('Invalid SKU ID.', 'error');
            $this->redirect(admin_url('index.php?page=product_skus&action=manage&product_id=' . $productId));
            return;
        }

        try {
            $this->skuModel->deleteSKU($id);
            $this->setFlash('SKU deleted successfully!', 'success');
        } catch (Exception $e) {
            $this->setFlash('Error deleting SKU: ' . $e->getMessage(), 'error');
        }

        $this->redirect(admin_url('index.php?page=product_skus&action=manage&product_id=' . $productId));
    }
}

