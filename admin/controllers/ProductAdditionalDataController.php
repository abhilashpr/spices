<?php
/**
 * Product Additional Data Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../config/database.php';

class ProductAdditionalDataController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireLogin();
    }

    /**
     * Manage product additional data (key-value pairs)
     */
    public function manage(): void
    {
        $productId = (int)($_GET['product_id'] ?? 0);

        if ($productId === 0) {
            $this->setFlash('Invalid product ID.', 'error');
            $this->redirect(admin_url('index.php?page=products'));
            return;
        }

        // Get product info
        require_once __DIR__ . '/../models/ProductModel.php';
        $productModel = new ProductModel();
        $product = $productModel->getById($productId);

        if (!$product) {
            $this->setFlash('Product not found.', 'error');
            $this->redirect(admin_url('index.php?page=products'));
            return;
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action']) && $_POST['action'] === 'delete') {
                $this->handleDelete($productId);
            } else {
                $this->handleSave($productId);
            }
        }

        // Get existing additional data
        $additionalData = [];
        try {
            $pdo = get_db_connection();
            $sql = "SELECT * FROM product_additional_data WHERE product_id = :product_id ORDER BY id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':product_id' => $productId]);
            $additionalData = $stmt->fetchAll();
        } catch (PDOException $e) {
            // Table might not exist
        }

        $flash = $this->getFlash();
        $this->render('product_additional_data/manage', [
            'product' => $product,
            'additionalData' => $additionalData,
            'flash' => $flash
        ]);
    }

    /**
     * Handle saving additional data
     */
    private function handleSave(int $productId): void
    {
        $dataKey = trim($_POST['data_key'] ?? '');
        $dataValue = trim($_POST['data_value'] ?? '');

        if (empty($dataKey) || empty($dataValue)) {
            $this->setFlash('Both key and value are required.', 'error');
            $this->redirect(admin_url('index.php?page=product_additional_data&action=manage&product_id=' . $productId));
            return;
        }

        try {
            $pdo = get_db_connection();
            
            // Create table if it doesn't exist
            $createTableSql = "CREATE TABLE IF NOT EXISTS product_additional_data (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                data_key VARCHAR(255) NOT NULL,
                data_value TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )";
            $pdo->exec($createTableSql);

            // Insert data
            $sql = "INSERT INTO product_additional_data (product_id, data_key, data_value) 
                    VALUES (:product_id, :data_key, :data_value)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':product_id' => $productId,
                ':data_key' => $dataKey,
                ':data_value' => $dataValue
            ]);

            $this->setFlash('Additional data added successfully!', 'success');
        } catch (PDOException $e) {
            $this->setFlash('Error saving additional data: ' . $e->getMessage(), 'error');
        }

        $this->redirect(admin_url('index.php?page=product_additional_data&action=manage&product_id=' . $productId));
    }

    /**
     * Handle deleting additional data
     */
    private function handleDelete(int $productId): void
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id === 0) {
            $this->setFlash('Invalid data ID.', 'error');
            $this->redirect(admin_url('index.php?page=product_additional_data&action=manage&product_id=' . $productId));
            return;
        }

        try {
            $pdo = get_db_connection();
            $sql = "DELETE FROM product_additional_data WHERE id = :id AND product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id, ':product_id' => $productId]);
            $this->setFlash('Additional data deleted successfully!', 'success');
        } catch (PDOException $e) {
            $this->setFlash('Error deleting data: ' . $e->getMessage(), 'error');
        }

        $this->redirect(admin_url('index.php?page=product_additional_data&action=manage&product_id=' . $productId));
    }
}

