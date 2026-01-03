<?php
/**
 * Admin Dashboard Entry Point
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/CategoryController.php';
require_once __DIR__ . '/controllers/SubcategoryController.php';
require_once __DIR__ . '/controllers/SliderController.php';
require_once __DIR__ . '/controllers/UnitController.php';
require_once __DIR__ . '/controllers/ProductController.php';
require_once __DIR__ . '/controllers/ProductSKUController.php';

Auth::requireLogin();

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Route to appropriate controller
switch ($page) {
    case 'categories':
        $controller = new CategoryController();
        switch ($action) {
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->create();
                } else {
                    $controller->index();
                }
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                $controller->index();
        }
        break;
    case 'subcategories':
        $controller = new SubcategoryController();
        switch ($action) {
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->create();
                } else {
                    $controller->index();
                }
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                $controller->index();
        }
        break;
    case 'sliders':
        $controller = new SliderController();
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'toggleActive':
                $controller->toggleActive();
                break;
            default:
                $controller->index();
        }
        break;
    case 'units':
        $controller = new UnitController();
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'toggleActive':
                $controller->toggleActive();
                break;
            default:
                $controller->index();
        }
        break;
    case 'products':
        $controller = new ProductController();
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'detail':
                $controller->detail();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                $controller->index();
        }
        break;
    case 'product_skus':
        $controller = new ProductSKUController();
        switch ($action) {
            case 'manage':
                $controller->manage();
                break;
            default:
                header('Location: ' . admin_url('index.php?page=products'));
                exit;
        }
        break;
    case 'product_additional_data':
        require_once __DIR__ . '/controllers/ProductAdditionalDataController.php';
        $controller = new ProductAdditionalDataController();
        switch ($action) {
            case 'manage':
                $controller->manage();
                break;
            default:
                header('Location: ' . admin_url('index.php?page=products'));
                exit;
        }
        break;
    case 'product_benefits':
        require_once __DIR__ . '/controllers/ProductBenefitController.php';
        $controller = new ProductBenefitController();
        switch ($action) {
            case 'manage':
                $controller->manage();
                break;
            default:
                header('Location: ' . admin_url('index.php?page=products'));
                exit;
        }
        break;
    default:
        $controller = new DashboardController();
        $controller->index();
}
