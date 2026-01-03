<?php
/**
 * Slider Controller
 * Handles all slider-related requests and responses
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/SliderModel.php';

class SliderController extends Controller
{
    private $sliderModel;

    public function __construct()
    {
        parent::__construct();
        Auth::requireLogin();
        $this->sliderModel = new SliderModel();
    }

    /**
     * Display all sliders
     */
    public function index(): void
    {
        $page = (int)($_GET['p'] ?? 1);
        $perPage = 10;
        $result = $this->sliderModel->getPaginated($page, $perPage);
        $flash = $this->getFlash();

        $this->render('sliders/index', [
            'sliders' => $result['data'],
            'pagination' => $result,
            'flash' => $flash
        ]);
    }

    /**
     * Create a new slider
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
        } else {
            $this->render('sliders/form', ['slider' => null]);
        }
    }

    /**
     * Handle slider creation POST request
     */
    private function handleCreate(): void
    {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sliderType = trim($_POST['slider_type'] ?? 'static');
        $linkUrl = trim($_POST['link_url'] ?? '');
        $linkText = trim($_POST['link_text'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $displayOrder = (int)($_POST['display_order'] ?? 0);

        // Validation
        if (empty($title)) {
            $this->setFlash('Title is required.', 'error');
            $this->redirect(admin_url('index.php?page=sliders'));
            return;
        }

        // Handle file upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = handle_file_upload(
                $_FILES['image'],
                SLIDER_UPLOAD_DIR,
                ALLOWED_IMAGE_TYPES,
                MAX_FILE_SIZE
            );

            if (!$uploadResult['success']) {
                $this->setFlash('Image upload failed: ' . $uploadResult['error'], 'error');
                $this->redirect(admin_url('index.php?page=sliders'));
                return;
            }

            $imagePath = $uploadResult['path'];
        } else {
            $this->setFlash('Image file is required.', 'error');
            $this->redirect(admin_url('index.php?page=sliders'));
            return;
        }

        if ($sliderType === 'link' && empty($linkUrl)) {
            $this->setFlash('Link URL is required for link-type sliders.', 'error');
            // Delete uploaded file if validation fails
            if ($imagePath) {
                delete_uploaded_file($imagePath);
            }
            $this->redirect(admin_url('index.php?page=sliders'));
            return;
        }

        // Validate slider type
        if (!in_array($sliderType, ['static', 'link'])) {
            $sliderType = 'static';
        }

        try {
            $data = [
                'title' => $title,
                'description' => !empty($description) ? $description : null,
                'image_url' => $imagePath,
                'slider_type' => $sliderType,
                'is_active' => $isActive,
                'display_order' => $displayOrder
            ];

            if ($sliderType === 'link') {
                $data['link_url'] = $linkUrl;
                $data['link_text'] = !empty($linkText) ? $linkText : null;
            }

            $this->sliderModel->createSlider($data);
            $this->setFlash('Slider created successfully!', 'success');
            $this->redirect(admin_url('index.php?page=sliders'));
        } catch (Exception $e) {
            // Delete uploaded file if database operation fails
            if ($imagePath) {
                delete_uploaded_file($imagePath);
            }
            $this->setFlash('Error creating slider: ' . $e->getMessage(), 'error');
            $this->redirect(admin_url('index.php?page=sliders'));
        }
    }

    /**
     * Edit an existing slider
     */
    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id === 0) {
            $this->setFlash('Invalid slider ID.', 'error');
            $this->redirect(admin_url('index.php?page=sliders'));
            return;
        }

        $slider = $this->sliderModel->getById($id);

        if (!$slider) {
            $this->setFlash('Slider not found.', 'error');
            $this->redirect(admin_url('index.php?page=sliders'));
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleUpdate($id);
        } else {
            $this->render('sliders/form', ['slider' => $slider]);
        }
    }

    /**
     * Handle slider update POST request
     */
    private function handleUpdate(int $id): void
    {
        $slider = $this->sliderModel->getById($id);
        if (!$slider) {
            $this->setFlash('Slider not found.', 'error');
            $this->redirect(admin_url('index.php?page=sliders'));
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sliderType = trim($_POST['slider_type'] ?? 'static');
        $linkUrl = trim($_POST['link_url'] ?? '');
        $linkText = trim($_POST['link_text'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $displayOrder = (int)($_POST['display_order'] ?? 0);

        // Validation
        if (empty($title)) {
            $this->setFlash('Title is required.', 'error');
            $this->redirect(admin_url('index.php?page=sliders&action=edit&id=' . $id));
            return;
        }

        // Handle file upload (only if new file is uploaded)
        $imagePath = $slider['image_url']; // Keep existing image by default
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = handle_file_upload(
                $_FILES['image'],
                SLIDER_UPLOAD_DIR,
                ALLOWED_IMAGE_TYPES,
                MAX_FILE_SIZE
            );

            if (!$uploadResult['success']) {
                $this->setFlash('Image upload failed: ' . $uploadResult['error'], 'error');
                $this->redirect(admin_url('index.php?page=sliders&action=edit&id=' . $id));
                return;
            }

            // Delete old image file
            if (!empty($slider['image_url'])) {
                delete_uploaded_file($slider['image_url']);
            }

            $imagePath = $uploadResult['path'];
        }

        if ($sliderType === 'link' && empty($linkUrl)) {
            $this->setFlash('Link URL is required for link-type sliders.', 'error');
            // Delete uploaded file if validation fails
            if ($imagePath !== $slider['image_url']) {
                delete_uploaded_file($imagePath);
            }
            $this->redirect(admin_url('index.php?page=sliders&action=edit&id=' . $id));
            return;
        }

        // Validate slider type
        if (!in_array($sliderType, ['static', 'link'])) {
            $sliderType = 'static';
        }

        try {
            $data = [
                'title' => $title,
                'description' => !empty($description) ? $description : null,
                'image_url' => $imagePath,
                'slider_type' => $sliderType,
                'is_active' => $isActive,
                'display_order' => $displayOrder
            ];

            if ($sliderType === 'link') {
                $data['link_url'] = $linkUrl;
                $data['link_text'] = !empty($linkText) ? $linkText : null;
            } else {
                // Clear link fields for static sliders
                $data['link_url'] = null;
                $data['link_text'] = null;
            }

            $this->sliderModel->updateSlider($id, $data);
            $this->setFlash('Slider updated successfully!', 'success');
            $this->redirect(admin_url('index.php?page=sliders'));
        } catch (Exception $e) {
            // Delete uploaded file if database operation fails
            if ($imagePath !== $slider['image_url']) {
                delete_uploaded_file($imagePath);
            }
            $this->setFlash('Error updating slider: ' . $e->getMessage(), 'error');
            $this->redirect(admin_url('index.php?page=sliders&action=edit&id=' . $id));
        }
    }

    /**
     * Delete a slider
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(admin_url('index.php?page=sliders'));
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id === 0) {
            $this->setFlash('Invalid slider ID.', 'error');
            $this->redirect(admin_url('index.php?page=sliders'));
            return;
        }

        try {
            // Get slider to delete image file
            $slider = $this->sliderModel->getById($id);
            
            // Delete slider from database
            $this->sliderModel->deleteSlider($id);
            
            // Delete image file
            if ($slider && !empty($slider['image_url'])) {
                delete_uploaded_file($slider['image_url']);
            }
            
            $this->setFlash('Slider deleted successfully!', 'success');
        } catch (Exception $e) {
            $this->setFlash('Error deleting slider: ' . $e->getMessage(), 'error');
        }

        $this->redirect(admin_url('index.php?page=sliders'));
    }

    /**
     * Toggle slider active status
     */
    public function toggleActive(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(admin_url('index.php?page=sliders'));
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id === 0) {
            $this->setFlash('Invalid slider ID.', 'error');
            $this->redirect(admin_url('index.php?page=sliders'));
            return;
        }

        try {
            $this->sliderModel->toggleActive($id);
            $this->setFlash('Slider status updated successfully!', 'success');
        } catch (Exception $e) {
            $this->setFlash('Error updating slider status: ' . $e->getMessage(), 'error');
        }

        $this->redirect(admin_url('index.php?page=sliders'));
    }
}

