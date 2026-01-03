<?php
/**
 * Subcategory Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/SubcategoryModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';

class SubcategoryController extends Controller
{
    private $subcategoryModel;
    private $categoryModel;

    public function __construct()
    {
        parent::__construct();
        Auth::requireLogin();
        $this->subcategoryModel = new SubcategoryModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index(): void
    {
        $page = (int)($_GET['p'] ?? 1);
        $perPage = 10;
        $result = $this->subcategoryModel->getPaginated($page, $perPage);
        $categories = $this->categoryModel->getAll();
        $flash = $this->getFlash();

        $this->render('subcategories/index', [
            'subcategories' => $result['data'],
            'pagination' => $result,
            'categories' => $categories,
            'flash' => $flash
        ]);
    }

    public function create(): void
    {
        $categories = $this->categoryModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_id = (int)($_POST['category_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $display_order = (int)($_POST['display_order'] ?? 0);

            if (empty($name) || empty($slug) || $category_id === 0) {
                $this->setFlash('Category, name, and slug are required.', 'error');
                $this->redirect(admin_url('index.php?page=subcategories'));
            } elseif ($this->subcategoryModel->slugExists($slug, $category_id)) {
                $this->setFlash('Slug already exists for this category. Please choose a different one.', 'error');
                $this->redirect(admin_url('index.php?page=subcategories'));
            } else {
                try {
                    $this->subcategoryModel->createSubcategory([
                        'category_id' => $category_id,
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description ?: null,
                        'display_order' => $display_order
                    ]);
                    $this->setFlash('Subcategory created successfully!', 'success');
                    $this->redirect(admin_url('index.php?page=subcategories'));
                } catch (Exception $e) {
                    $this->setFlash('Error creating subcategory: ' . $e->getMessage(), 'error');
                    $this->redirect(admin_url('index.php?page=subcategories'));
                }
            }
        } else {
            $this->render('subcategories/form', [
                'subcategory' => null,
                'categories' => $categories
            ]);
        }
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $subcategory = $this->subcategoryModel->getById($id);
        $categories = $this->categoryModel->getAll();

        if (!$subcategory) {
            $this->setFlash('Subcategory not found.', 'error');
            $this->redirect(admin_url('index.php?page=subcategories'));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_id = (int)($_POST['category_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $display_order = (int)($_POST['display_order'] ?? 0);

            if (empty($name) || empty($slug) || $category_id === 0) {
                $this->setFlash('Category, name, and slug are required.', 'error');
                $this->redirect(admin_url('index.php?page=subcategories&action=edit&id=' . $id));
            } elseif ($this->subcategoryModel->slugExists($slug, $category_id, $id)) {
                $this->setFlash('Slug already exists for this category. Please choose a different one.', 'error');
                $this->redirect(admin_url('index.php?page=subcategories&action=edit&id=' . $id));
            } else {
                try {
                    $this->subcategoryModel->updateSubcategory($id, [
                        'category_id' => $category_id,
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description ?: null,
                        'display_order' => $display_order
                    ]);
                    $this->setFlash('Subcategory updated successfully!', 'success');
                    $this->redirect(admin_url('index.php?page=subcategories'));
                } catch (Exception $e) {
                    $this->setFlash('Error updating subcategory: ' . $e->getMessage(), 'error');
                    $this->redirect(admin_url('index.php?page=subcategories&action=edit&id=' . $id));
                }
            }
        } else {
            $this->render('subcategories/form', [
                'subcategory' => $subcategory,
                'categories' => $categories
            ]);
        }
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(admin_url('index.php?page=subcategories'));
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            try {
                $this->subcategoryModel->deleteSubcategory($id);
                $this->setFlash('Subcategory deleted successfully!', 'success');
            } catch (Exception $e) {
                $this->setFlash('Error deleting subcategory: ' . $e->getMessage(), 'error');
            }
        }

        $this->redirect(admin_url('index.php?page=subcategories'));
    }
}

