<?php
/**
 * Profile Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/UserModel.php';

class ProfileController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            $this->setFlash('Please login to access your profile', 'error');
            $this->redirect(url('login'));
        }
    }

    public function index(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            $this->setFlash('User not found', 'error');
            $this->redirect(url('login'));
            return;
        }

        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            $this->setFlash('User not found', 'error');
            $this->redirect(url('login'));
            return;
        }

        $this->render('profile/index', [
            'layout' => 'main',
            'pageTitle' => 'My Profile | Wynvalley',
            'user' => $user
        ]);
    }
}

