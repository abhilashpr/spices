<?php
/**
 * Authentication Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::isLoggedIn()) {
            $this->redirect(ADMIN_LOGIN_REDIRECT);
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Please enter both username and password.';
            } elseif (Auth::login($username, $password)) {
                $this->redirect(ADMIN_LOGIN_REDIRECT);
            } else {
                $error = 'Invalid username or password.';
            }
        }

        $this->render('auth/login', ['error' => $error]);
    }

    public function logout(): void
    {
        Auth::logout();
    }
}

