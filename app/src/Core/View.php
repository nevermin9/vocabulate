<?php
declare(strict_types=1);

namespace App\Core;


final class View
{

    public function __construct(
        private string $view,
        private array $params = [],
        private string $layout = 'main',
    )
    {
    }

    public static function make(string $view, array $params = [], string $layout = 'main'): static
    {
        return new static($view, $params, $layout);
    }

    private function render(): string
    {

        $view = $this->renderView();
        $layout = $this->renderLayout();

        return str_replace("{{content}}", $view, $layout);
    }

    private function renderLayout()
    {
        ob_start();

        require_once \LAYOUTS_DIR . "/" . $this->layout . ".php";

        return (string) ob_get_clean();
    }

    private function renderView()
    {
        ob_start();

        require_once \VIEWS_DIR . "/" . $this->view . ".php"; 

        return (string) ob_get_clean();
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function __toString(): string
    {
        return $this->render();
    }
}

