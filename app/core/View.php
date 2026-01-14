<?php
/**
 * View Renderer Class
 */

class View
{
    private $viewsPath;

    public function __construct()
    {
        $this->viewsPath = __DIR__ . '/../../views';
    }

    public function render(string $viewFile, array $data = []): void
    {
        // Get flash message from session
        if (!isset($data['flash'])) {
            $data['flash'] = $this->getFlash();
        }
        
        // Extract data array to variables
        extract($data);

        // Start output buffering
        ob_start();

        // Include the view file
        $viewPath = $this->viewsPath . '/' . ltrim($viewFile, '/') . '.php';
        
        if (!file_exists($viewPath)) {
            throw new Exception("View file not found: {$viewPath}");
        }

        include $viewPath;

        // Get the output
        $content = ob_get_clean();

        // If layout is specified, wrap in layout
        if (isset($layout) && $layout) {
            $layoutPath = $this->viewsPath . '/layouts/' . $layout . '.php';
            if (file_exists($layoutPath)) {
                // Extract data again for layout (including $content)
                extract($data);
                include $layoutPath;
                return;
            }
        }

        // Otherwise, output directly
        echo $content;
    }
    
    private function getFlash(): ?array
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

    public function partial(string $partialFile, array $data = []): void
    {
        extract($data);
        $partialPath = $this->viewsPath . '/partials/' . ltrim($partialFile, '/') . '.php';
        
        if (file_exists($partialPath)) {
            include $partialPath;
        }
    }
}

