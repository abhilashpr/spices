<?php
/**
 * Category Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/CategoryModel.php';

class CategoryController extends Controller
{
    private $categoryModel;

    public function __construct()
    {
        parent::__construct();
        Auth::requireLogin();
        $this->categoryModel = new CategoryModel();
    }

    public function index(): void
    {
        $page = (int)($_GET['p'] ?? 1);
        $perPage = 10;
        $result = $this->categoryModel->getPaginated($page, $perPage);
        $flash = $this->getFlash();

        $this->render('categories/index', [
            'categories' => $result['data'],
            'pagination' => $result,
            'flash' => $flash
        ]);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $display_order = (int)($_POST['display_order'] ?? 0);

            if (empty($name) || empty($slug)) {
                $this->setFlash('Name and slug are required.', 'error');
                $this->redirect(admin_url('index.php?page=categories'));
            } elseif ($this->categoryModel->slugExists($slug)) {
                $this->setFlash('Slug already exists. Please choose a different one.', 'error');
                $this->redirect(admin_url('index.php?page=categories'));
            } else {
                try {
                    $this->categoryModel->createCategory([
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description ?: null,
                        'display_order' => $display_order
                    ]);
                    $this->setFlash('Category created successfully!', 'success');
                    $this->redirect(admin_url('index.php?page=categories'));
                } catch (Exception $e) {
                    $this->setFlash('Error creating category: ' . $e->getMessage(), 'error');
                    $this->redirect(admin_url('index.php?page=categories'));
                }
            }
        } else {
            $this->render('categories/form', ['category' => null]);
        }
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $category = $this->categoryModel->getById($id);

        if (!$category) {
            $this->setFlash('Category not found.', 'error');
            $this->redirect(admin_url('index.php?page=categories'));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $display_order = (int)($_POST['display_order'] ?? 0);

            if (empty($name) || empty($slug)) {
                $this->setFlash('Name and slug are required.', 'error');
                $this->redirect(admin_url('index.php?page=categories&action=edit&id=' . $id));
            } elseif ($this->categoryModel->slugExists($slug, $id)) {
                $this->setFlash('Slug already exists. Please choose a different one.', 'error');
                $this->redirect(admin_url('index.php?page=categories&action=edit&id=' . $id));
            } else {
                try {
                    $this->categoryModel->updateCategory($id, [
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description ?: null,
                        'display_order' => $display_order
                    ]);
                    $this->setFlash('Category updated successfully!', 'success');
                    $this->redirect(admin_url('index.php?page=categories'));
                } catch (Exception $e) {
                    $this->setFlash('Error updating category: ' . $e->getMessage(), 'error');
                    $this->redirect(admin_url('index.php?page=categories&action=edit&id=' . $id));
                }
            }
        } else {
            $this->render('categories/form', ['category' => $category]);
        }
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(admin_url('index.php?page=categories'));
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            try {
                $this->categoryModel->deleteCategory($id);
                $this->setFlash('Category deleted successfully!', 'success');
            } catch (Exception $e) {
                $this->setFlash('Error deleting category: ' . $e->getMessage(), 'error');
            }
        }

        $this->redirect(admin_url('index.php?page=categories'));
    }
}

