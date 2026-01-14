<?php
/**
 * Dashboard Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireLogin();
    }

    public function index(): void
    {
        $this->render('dashboard/index', [
            'username' => Auth::getUsername()
        ]);
    }
}



