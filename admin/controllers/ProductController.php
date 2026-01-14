<?php
/**
 * Product Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/ProductModel.php';

class ProductController extends Controller
{
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        Auth::requireLogin();
        $this->productModel = new ProductModel();
    }

    /**
     * Display all products (list page)
     */
    public function index(): void
    {
        $page = (int)($_GET['p'] ?? 1);
        $perPage = 10;
        $result = $this->productModel->getPaginated($page, $perPage);
        $flash = $this->getFlash();

        $this->render('products/index', [
            'products' => $result['data'],
            'pagination' => $result,
            'flash' => $flash
        ]);
    }

    /**
     * Show create form (separate page)
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
        } else {
            // Load categories and subcategories for the form
            require_once __DIR__ . '/../models/CategoryModel.php';
            require_once __DIR__ . '/../models/SubcategoryModel.php';
            $categoryModel = new CategoryModel();
            $subcategoryModel = new SubcategoryModel();
            
            $categories = $categoryModel->getAll();
            $allSubcategories = $subcategoryModel->getAll();
            
            $flash = $this->getFlash();
            $this->render('products/create', [
                'product' => null,
                'categories' => $categories,
                'subcategories' => $allSubcategories,
                'flash' => $flash
            ]);
        }
    }

    /**
     * Handle product creation POST request
     */
    private function handleCreate(): void
    {
        // Get form data
        $productCode = trim($_POST['product_code'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $subcategoryId = !empty($_POST['subcategory_id']) ? (int)$_POST['subcategory_id'] : null;
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $isOutOfStock = isset($_POST['is_out_of_stock']) ? 1 : 0;

        // Get language data
        $languages = [];
        if (isset($_POST['language_code']) && is_array($_POST['language_code'])) {
            foreach ($_POST['language_code'] as $index => $code) {
                if (!empty($code) && !empty($_POST['language_name'][$index])) {
                    $languages[] = [
                        'code' => trim($code),
                        'name' => trim($_POST['language_name'][$index])
                    ];
                }
            }
        }

        // Validation
        if (empty($title)) {
            $this->setFlash('Product title is required.', 'error');
            $this->redirect(admin_url('index.php?page=products&action=create'));
            return;
        }

        if (empty($slug)) {
            $this->setFlash('Slug is required.', 'error');
            $this->redirect(admin_url('index.php?page=products&action=create'));
            return;
        }
        
        if (empty($categoryId)) {
            $this->setFlash('Category is required.', 'error');
            $this->redirect(admin_url('index.php?page=products&action=create'));
            return;
        }

        if ($this->productModel->slugExists($slug)) {
            $this->setFlash('Slug already exists. Please choose a different one.', 'error');
            $this->redirect(admin_url('index.php?page=products&action=create'));
            return;
        }

        if (!empty($productCode) && $this->productModel->productCodeExists($productCode)) {
            $this->setFlash('Product code already exists. Please choose a different one.', 'error');
            $this->redirect(admin_url('index.php?page=products&action=create'));
            return;
        }

        // Handle main image upload
        $mainImagePath = null;
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = handle_file_upload(
                $_FILES['main_image'],
                PRODUCT_UPLOAD_DIR,
                ALLOWED_IMAGE_TYPES,
                MAX_FILE_SIZE,
                'product_main_'
            );

            if (!$uploadResult['success']) {
                $this->setFlash('Main image upload failed: ' . $uploadResult['error'], 'error');
                $this->redirect(admin_url('index.php?page=products&action=create'));
                return;
            }

            $mainImagePath = $uploadResult['path'];
        } else {
            $this->setFlash('Main image is required.', 'error');
            $this->redirect(admin_url('index.php?page=products&action=create'));
            return;
        }

        // Handle additional images upload
        $additionalImagePaths = [];
        if (isset($_FILES['additional_images']) && is_array($_FILES['additional_images']['name'])) {
            $fileCount = count($_FILES['additional_images']['name']);
            
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['additional_images']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                    $file = [
                        'name' => $_FILES['additional_images']['name'][$i],
                        'type' => $_FILES['additional_images']['type'][$i],
                        'tmp_name' => $_FILES['additional_images']['tmp_name'][$i],
                        'error' => $_FILES['additional_images']['error'][$i],
                        'size' => $_FILES['additional_images']['size'][$i]
                    ];

                    $uploadResult = handle_file_upload(
                        $file,
                        PRODUCT_UPLOAD_DIR,
                        ALLOWED_IMAGE_TYPES,
                        MAX_FILE_SIZE,
                        'product_additional_'
                    );

                    if ($uploadResult['success']) {
                        $additionalImagePaths[] = $uploadResult['path'];
                    }
                }
            }
        }

        try {
            // Prepare product data - only include fields that exist in the table
            $productData = [
                'name' => $title,
                'slug' => $slug,
                'category_id' => $categoryId,
                'group_id' => !empty($subcategoryId) ? $subcategoryId : null, // subcategory_id stored as group_id
            ];
            
            // Add optional fields
            $productData['product_code'] = !empty($productCode) ? $productCode : null;
            if (!empty($description)) {
                $productData['description'] = $description;
            }
            // Don't set summary if description is empty - let the model handle defaults
            
            if (!empty($mainImagePath)) {
                $productData['main_image'] = $mainImagePath;
            }
            
            // Handle boolean fields - ensure they are integers, not empty strings
            $productData['is_active'] = (int)$isActive;
            $productData['is_out_of_stock'] = (int)$isOutOfStock;
            
            // For old table structure compatibility - provide defaults for required fields
            // These will be filtered out if columns don't exist
            $productData['image_class'] = 'default'; // Default image class
            $productData['region'] = 'general'; // Default region
            $productData['craft'] = 'blended'; // Default craft
            $productData['heat'] = 'mild'; // Default heat
            
            // Create product
            $productId = $this->productModel->createProduct($productData);

            // Save product languages
            if (!empty($languages)) {
                $this->productModel->saveProductLanguages($productId, $languages);
            }

            // Save additional images
            if (!empty($additionalImagePaths)) {
                $this->productModel->saveProductImages($productId, $additionalImagePaths);
            }

            $this->setFlash('Product created successfully!', 'success');
            $this->redirect(admin_url('index.php?page=products'));
        } catch (Exception $e) {
            // Clean up uploaded files on error
            if ($mainImagePath) {
                delete_uploaded_file($mainImagePath);
            }
            foreach ($additionalImagePaths as $imgPath) {
                delete_uploaded_file($imgPath);
            }

            $this->setFlash('Error creating product: ' . $e->getMessage(), 'error');
            $this->redirect(admin_url('index.php?page=products&action=create'));
        }
    }

    /**
     * Display product detail page
     */
    public function detail(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id === 0) {
            $this->setFlash('Invalid product ID.', 'error');
            $this->redirect(admin_url('index.php?page=products'));
            return;
        }

        $product = $this->productModel->getById($id);

        if (!$product) {
            $this->setFlash('Product not found.', 'error');
            $this->redirect(admin_url('index.php?page=products'));
            return;
        }

        // Get SKUs
        $skus = [];
        try {
            require_once __DIR__ . '/../models/ProductSKUModel.php';
            $skuModel = new ProductSKUModel();
            $skus = $skuModel->getByProductId($id);
        } catch (Exception $e) {
            $skus = [];
        }
        
        // Get additional data and benefits if tables exist
        $additionalData = [];
        $benefits = [];
        
        try {
            require_once __DIR__ . '/../config/database.php';
            $pdo = get_db_connection();
            $sql = "SELECT * FROM product_additional_data WHERE product_id = :product_id ORDER BY id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':product_id' => $id]);
            $additionalData = $stmt->fetchAll();
        } catch (PDOException $e) {
            // Table might not exist - ignore
        }
        
        try {
            require_once __DIR__ . '/../config/database.php';
            $pdo = get_db_connection();
            $sql = "SELECT * FROM product_benefits WHERE product_id = :product_id ORDER BY benefit_type, id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':product_id' => $id]);
            $benefits = $stmt->fetchAll();
        } catch (PDOException $e) {
            // Table might not exist - ignore
        }

        // Group benefits by type
        $benefitsByType = [
            'health_benefits' => [],
            'how_to_use' => [],
            'how_to_store' => []
        ];
        
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

        $flash = $this->getFlash();
        $this->render('products/detail', [
            'product' => $product,
            'skus' => $skus ?? [],
            'additionalData' => $additionalData ?? [],
            'benefitsByType' => $benefitsByType,
            'flash' => $flash
        ]);
    }

    /**
     * Show edit form (separate page)
     */
    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id === 0) {
            $this->setFlash('Invalid product ID.', 'error');
            $this->redirect(admin_url('index.php?page=products'));
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleUpdate($id);
        } else {
            $product = $this->productModel->getById($id);

            if (!$product) {
                $this->setFlash('Product not found.', 'error');
                $this->redirect(admin_url('index.php?page=products'));
                return;
            }

            // Load categories and subcategories for the form
            require_once __DIR__ . '/../models/CategoryModel.php';
            require_once __DIR__ . '/../models/SubcategoryModel.php';
            $categoryModel = new CategoryModel();
            $subcategoryModel = new SubcategoryModel();
            
            $categories = $categoryModel->getAll();
            $allSubcategories = $subcategoryModel->getAll();

            $flash = $this->getFlash();
            $this->render('products/edit', [
                'product' => $product,
                'categories' => $categories,
                'subcategories' => $allSubcategories,
                'flash' => $flash
            ]);
        }
    }

    /**
     * Handle product update POST request
     */
    private function handleUpdate(int $id): void
    {
        // Get form data
        $productCode = trim($_POST['product_code'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $subcategoryId = (int)($_POST['subcategory_id'] ?? 0); // Optional
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $isOutOfStock = isset($_POST['is_out_of_stock']) ? 1 : 0;

        // Get language data
        $languages = [];
        if (isset($_POST['language_code']) && is_array($_POST['language_code'])) {
            foreach ($_POST['language_code'] as $index => $code) {
                if (!empty($code) && !empty($_POST['language_name'][$index])) {
                    $languages[] = [
                        'code' => trim($code),
                        'name' => trim($_POST['language_name'][$index])
                    ];
                }
            }
        }

        // Validation
        if (empty($title)) {
            $this->setFlash('Product title is required.', 'error');
            $this->redirect(admin_url('index.php?page=products&action=edit&id=' . $id));
            return;
        }

        if (empty($slug)) {
            $this->setFlash('Slug is required.', 'error');
            $this->redirect(admin_url('index.php?page=products&action=edit&id=' . $id));
            return;
        }

        // Check if slug exists for another product
        if ($this->productModel->slugExists($slug, $id)) {
            $this->setFlash('Slug already exists. Please choose a different one.', 'error');
            $this->redirect(admin_url('index.php?page=products&action=edit&id=' . $id));
            return;
        }

        // Check if product code exists for another product
        if (!empty($productCode) && $this->productModel->productCodeExists($productCode, $id)) {
            $this->setFlash('Product code already exists. Please choose a different one.', 'error');
            $this->redirect(admin_url('index.php?page=products&action=edit&id=' . $id));
            return;
        }
        
        // Validation
        if (empty($categoryId)) {
            $this->setFlash('Category is required.', 'error');
            $this->redirect(admin_url('index.php?page=products&action=edit&id=' . $id));
            return;
        }

        // Get existing product data
        $existingProduct = $this->productModel->getById($id);
        if (!$existingProduct) {
            $this->setFlash('Product not found.', 'error');
            $this->redirect(admin_url('index.php?page=products'));
            return;
        }

        // Handle main image upload (only if new file is uploaded)
        $mainImagePath = $existingProduct['main_image'] ?? null;
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = handle_file_upload(
                $_FILES['main_image'],
                PRODUCT_UPLOAD_DIR,
                ALLOWED_IMAGE_TYPES,
                MAX_FILE_SIZE,
                'product_main_'
            );

            if (!$uploadResult['success']) {
                $this->setFlash('Main image upload failed: ' . $uploadResult['error'], 'error');
                $this->redirect(admin_url('index.php?page=products&action=edit&id=' . $id));
                return;
            }

            // Delete old image if it exists
            if ($mainImagePath) {
                delete_uploaded_file($mainImagePath);
            }

            $mainImagePath = $uploadResult['path'];
        }

        // Handle additional images upload
        $additionalImagePaths = [];
        if (isset($_FILES['additional_images']) && is_array($_FILES['additional_images']['name'])) {
            $fileCount = count($_FILES['additional_images']['name']);
            
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['additional_images']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                    $file = [
                        'name' => $_FILES['additional_images']['name'][$i],
                        'type' => $_FILES['additional_images']['type'][$i],
                        'tmp_name' => $_FILES['additional_images']['tmp_name'][$i],
                        'error' => $_FILES['additional_images']['error'][$i],
                        'size' => $_FILES['additional_images']['size'][$i]
                    ];

                    $uploadResult = handle_file_upload(
                        $file,
                        PRODUCT_UPLOAD_DIR,
                        ALLOWED_IMAGE_TYPES,
                        MAX_FILE_SIZE,
                        'product_additional_'
                    );

                    if ($uploadResult['success']) {
                        $additionalImagePaths[] = $uploadResult['path'];
                    }
                }
            }
        }

        // Handle existing additional images (if keep_existing_images is set)
        if (isset($_POST['keep_existing_images']) && is_array($_POST['keep_existing_images'])) {
            foreach ($_POST['keep_existing_images'] as $imageId) {
                $imageId = (int)$imageId;
                if ($imageId > 0 && isset($existingProduct['images']) && is_array($existingProduct['images'])) {
                    foreach ($existingProduct['images'] as $img) {
                        if (isset($img['id']) && $img['id'] == $imageId && isset($img['image_path'])) {
                            $additionalImagePaths[] = $img['image_path'];
                            break;
                        }
                    }
                }
            }
        }

        try {
            // Prepare product data
            $productData = [
                'name' => $title,
                'slug' => $slug,
                'category_id' => $categoryId,
            ];
            
            // Add optional fields
            $productData['product_code'] = !empty($productCode) ? $productCode : null;
            $productData['group_id'] = $subcategoryId > 0 ? $subcategoryId : null;
            
            if (!empty($description)) {
                $productData['description'] = $description;
            } else {
                $productData['description'] = null;
            }
            
            if (!empty($mainImagePath)) {
                $productData['main_image'] = $mainImagePath;
            }
            
            // Handle boolean fields
            $productData['is_active'] = (int)$isActive;
            $productData['is_out_of_stock'] = (int)$isOutOfStock;
            
            // Update product
            $this->productModel->updateProduct($id, $productData);

            // Save product languages
            $this->productModel->saveProductLanguages($id, $languages);

            // Save additional images (this will replace all existing images)
            $this->productModel->saveProductImages($id, $additionalImagePaths);

            $this->setFlash('Product updated successfully!', 'success');
            $this->redirect(admin_url('index.php?page=products'));
        } catch (Exception $e) {
            // Clean up uploaded files on error
            if ($mainImagePath && $mainImagePath !== ($existingProduct['main_image'] ?? null)) {
                delete_uploaded_file($mainImagePath);
            }
            foreach ($additionalImagePaths as $imgPath) {
                // Only delete if it's a new upload (not from existing images)
                if (strpos($imgPath, 'product_additional_') !== false) {
                    delete_uploaded_file($imgPath);
                }
            }

            $this->setFlash('Error updating product: ' . $e->getMessage(), 'error');
            $this->redirect(admin_url('index.php?page=products&action=edit&id=' . $id));
        }
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(admin_url('index.php?page=products'));
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id === 0) {
            $this->setFlash('Invalid product ID.', 'error');
            $this->redirect(admin_url('index.php?page=products'));
            return;
        }

        try {
            $this->productModel->deleteProduct($id);
            $this->setFlash('Product deleted successfully!', 'success');
        } catch (Exception $e) {
            $this->setFlash('Error deleting product: ' . $e->getMessage(), 'error');
        }

        $this->redirect(admin_url('index.php?page=products'));
    }
}

