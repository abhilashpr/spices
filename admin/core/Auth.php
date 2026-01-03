<?php
/**
 * Authentication Handler
 */

class Auth
{
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: ' . ADMIN_LOGOUT_REDIRECT);
            exit;
        }
    }

    public static function login(string $username, string $password): bool
    {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare('SELECT id, username, password FROM admins WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: ' . ADMIN_LOGOUT_REDIRECT);
        exit;
    }

    public static function getUserId(): ?int
    {
        return $_SESSION['admin_id'] ?? null;
    }

    public static function getUsername(): ?string
    {
        return $_SESSION['admin_username'] ?? null;
    }
}

