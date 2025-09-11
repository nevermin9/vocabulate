<?php
declare(strict_types=1);

namespace App;


final class View
{
    public function __construct(private string $view, private array $params = [])
    {
    }

    public static function make(string $view, array $params = []): static
    {
        return new static($view, $params);
    }

    private function render(): string
    {
        $templatePath = \VIEWS_DIR . "/" . $this->view . ".php";

        if (!file_exists($templatePath)) {
            throw new \Exception('implement error if view doesnt exist');
        }

        ob_start();

        include $templatePath;

        return (string) ob_get_clean();
    }

    public function __toString(): string
    {
        return $this->render();
    }

}

