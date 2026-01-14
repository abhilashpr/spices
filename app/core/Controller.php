<?php
/**
 * Base Controller Class for Frontend
 */

require_once __DIR__ . '/View.php';

abstract class Controller
{
    protected $view;
    protected $data = [];

    public function __construct()
    {
        $this->view = new View();
    }

    protected function render(string $viewFile, array $data = []): void
    {
        $this->view->render($viewFile, array_merge($this->data, $data));
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function setFlash(string $message, string $type = 'success'): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }

    protected function getFlash(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['flash_message'])) {
            $flash = [
                'message' => $_SESSION['flash_message'],
                'type' => $_SESSION['flash_type'] ?? 'success'
            ];
            unset($_SESSION['flash_message'], $_SESSION['flash_type']);
            return $flash;
        }
        return null;
    }

    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

