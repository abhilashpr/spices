<?php
/**
 * View Renderer
 */

class View
{
    private $basePath;

    public function __construct()
    {
        $this->basePath = ADMIN_VIEWS;
    }

    public function render(string $viewFile, array $data = []): void
    {
        extract($data);
        
        $viewPath = $this->basePath . '/' . $viewFile . '.php';

        if (!file_exists($viewPath)) {
            throw new Exception("View file not found: {$viewFile}");
        }

        require $viewPath;
    }

    public function partial(string $partialFile, array $data = []): void
    {
        extract($data);
        $partialPath = $this->basePath . '/partials/' . $partialFile . '.php';

        if (!file_exists($partialPath)) {
            throw new Exception("Partial file not found: {$partialFile}");
        }

        require $partialPath;
    }
}

