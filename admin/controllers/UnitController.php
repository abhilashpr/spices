<?php
/**
 * Unit Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/UnitModel.php';

class UnitController extends Controller
{
    private $unitModel;

    public function __construct()
    {
        parent::__construct();
        Auth::requireLogin();
        $this->unitModel = new UnitModel();
    }

    public function index(): void
    {
        $units = $this->unitModel->getAll();
        $flash = $this->getFlash();

        $this->render('units/index', [
            'units' => $units,
            'flash' => $flash
        ]);
    }

    public function form(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $unit = null;
        
        if ($id > 0) {
            $unit = $this->unitModel->getById($id);
            if (!$unit) {
                $this->setFlash('Unit not found.', 'error');
                $this->redirect(admin_url('index.php?page=units'));
                return;
            }
        }

        $flash = $this->getFlash();

        $this->render('units/form', [
            'unit' => $unit,
            'flash' => $flash
        ]);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(admin_url('index.php?page=units'));
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $symbol = trim($_POST['symbol'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $displayOrder = (int)($_POST['display_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($name) || empty($symbol)) {
            $this->setFlash('Name and symbol are required.', 'error');
            $this->redirect(admin_url('index.php?page=units&action=create'));
            return;
        }

        if ($this->unitModel->nameExists($name)) {
            $this->setFlash('Unit name already exists. Please choose a different one.', 'error');
            $this->redirect(admin_url('index.php?page=units&action=create'));
            return;
        }

        if ($this->unitModel->symbolExists($symbol)) {
            $this->setFlash('Unit symbol already exists. Please choose a different one.', 'error');
            $this->redirect(admin_url('index.php?page=units&action=create'));
            return;
        }

        try {
            $this->unitModel->createUnit([
                'name' => $name,
                'symbol' => $symbol,
                'description' => $description ?: null,
                'display_order' => $displayOrder,
                'is_active' => $isActive
            ]);
            $this->setFlash('Unit created successfully!', 'success');
            $this->redirect(admin_url('index.php?page=units'));
        } catch (Exception $e) {
            $this->setFlash('Error creating unit: ' . $e->getMessage(), 'error');
            $this->redirect(admin_url('index.php?page=units&action=create'));
        }
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id === 0) {
            $this->setFlash('Invalid unit ID.', 'error');
            $this->redirect(admin_url('index.php?page=units'));
            return;
        }

        $unit = $this->unitModel->getById($id);

        if (!$unit) {
            $this->setFlash('Unit not found.', 'error');
            $this->redirect(admin_url('index.php?page=units'));
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $symbol = trim($_POST['symbol'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $displayOrder = (int)($_POST['display_order'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if (empty($name) || empty($symbol)) {
                $this->setFlash('Name and symbol are required.', 'error');
                $this->redirect(admin_url('index.php?page=units&action=edit&id=' . $id));
                return;
            }

            if ($this->unitModel->nameExists($name, $id)) {
                $this->setFlash('Unit name already exists. Please choose a different one.', 'error');
                $this->redirect(admin_url('index.php?page=units&action=edit&id=' . $id));
                return;
            }

            if ($this->unitModel->symbolExists($symbol, $id)) {
                $this->setFlash('Unit symbol already exists. Please choose a different one.', 'error');
                $this->redirect(admin_url('index.php?page=units&action=edit&id=' . $id));
                return;
            }

            try {
                $this->unitModel->updateUnit($id, [
                    'name' => $name,
                    'symbol' => $symbol,
                    'description' => $description ?: null,
                    'display_order' => $displayOrder,
                    'is_active' => $isActive
                ]);
                $this->setFlash('Unit updated successfully!', 'success');
                $this->redirect(admin_url('index.php?page=units'));
            } catch (Exception $e) {
                $this->setFlash('Error updating unit: ' . $e->getMessage(), 'error');
                $this->redirect(admin_url('index.php?page=units&action=edit&id=' . $id));
            }
        } else {
            $flash = $this->getFlash();
            $this->render('units/form', [
                'unit' => $unit,
                'flash' => $flash
            ]);
        }
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(admin_url('index.php?page=units'));
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id === 0) {
            $this->setFlash('Invalid unit ID.', 'error');
            $this->redirect(admin_url('index.php?page=units'));
            return;
        }

        try {
            $this->unitModel->deleteUnit($id);
            $this->setFlash('Unit deleted successfully!', 'success');
        } catch (Exception $e) {
            $this->setFlash('Error deleting unit: ' . $e->getMessage(), 'error');
        }

        $this->redirect(admin_url('index.php?page=units'));
    }

    public function toggleActive(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(admin_url('index.php?page=units'));
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id === 0) {
            $this->setFlash('Invalid unit ID.', 'error');
            $this->redirect(admin_url('index.php?page=units'));
            return;
        }

        try {
            $this->unitModel->toggleActive($id);
            $this->setFlash('Unit status updated successfully!', 'success');
        } catch (Exception $e) {
            $this->setFlash('Error updating unit status: ' . $e->getMessage(), 'error');
        }

        $this->redirect(admin_url('index.php?page=units'));
    }
}

