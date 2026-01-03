<?php
/**
 * Product Benefit Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../config/database.php';

class ProductBenefitController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireLogin();
    }

    /**
     * Manage product benefits
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

        // Get existing benefits grouped by type
        $benefits = [
            'health_benefits' => [],
            'how_to_use' => [],
            'how_to_store' => []
        ];

        try {
            $pdo = get_db_connection();
            $sql = "SELECT * FROM product_benefits WHERE product_id = :product_id ORDER BY benefit_type, id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':product_id' => $productId]);
            $allBenefits = $stmt->fetchAll();

            foreach ($allBenefits as $benefit) {
                $type = $benefit['benefit_type'] ?? '';
                if (isset($benefits[$type])) {
                    $benefits[$type][] = $benefit;
                }
            }
        } catch (PDOException $e) {
            // Table might not exist
        }

        $flash = $this->getFlash();
        $this->render('product_benefits/manage', [
            'product' => $product,
            'benefits' => $benefits,
            'flash' => $flash
        ]);
    }

    /**
     * Handle saving benefit
     */
    private function handleSave(int $productId): void
    {
        $benefitType = trim($_POST['benefit_type'] ?? '');
        $benefitText = trim($_POST['benefit_text'] ?? '');

        if (empty($benefitType) || empty($benefitText)) {
            $this->setFlash('Both benefit type and text are required.', 'error');
            $this->redirect(admin_url('index.php?page=product_benefits&action=manage&product_id=' . $productId));
            return;
        }

        if (!in_array($benefitType, ['health_benefits', 'how_to_use', 'how_to_store'])) {
            $this->setFlash('Invalid benefit type.', 'error');
            $this->redirect(admin_url('index.php?page=product_benefits&action=manage&product_id=' . $productId));
            return;
        }

        try {
            $pdo = get_db_connection();
            
            // Create table if it doesn't exist
            $createTableSql = "CREATE TABLE IF NOT EXISTS product_benefits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                benefit_type ENUM('health_benefits', 'how_to_use', 'how_to_store') NOT NULL,
                benefit_text TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )";
            $pdo->exec($createTableSql);

            // Insert benefit
            $sql = "INSERT INTO product_benefits (product_id, benefit_type, benefit_text) 
                    VALUES (:product_id, :benefit_type, :benefit_text)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':product_id' => $productId,
                ':benefit_type' => $benefitType,
                ':benefit_text' => $benefitText
            ]);

            $this->setFlash('Benefit added successfully!', 'success');
        } catch (PDOException $e) {
            $this->setFlash('Error saving benefit: ' . $e->getMessage(), 'error');
        }

        $this->redirect(admin_url('index.php?page=product_benefits&action=manage&product_id=' . $productId));
    }

    /**
     * Handle deleting benefit
     */
    private function handleDelete(int $productId): void
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id === 0) {
            $this->setFlash('Invalid benefit ID.', 'error');
            $this->redirect(admin_url('index.php?page=product_benefits&action=manage&product_id=' . $productId));
            return;
        }

        try {
            $pdo = get_db_connection();
            $sql = "DELETE FROM product_benefits WHERE id = :id AND product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id, ':product_id' => $productId]);
            $this->setFlash('Benefit deleted successfully!', 'success');
        } catch (PDOException $e) {
            $this->setFlash('Error deleting benefit: ' . $e->getMessage(), 'error');
        }

        $this->redirect(admin_url('index.php?page=product_benefits&action=manage&product_id=' . $productId));
    }
}

