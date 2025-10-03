<?php
declare(strict_types=1);

namespace App\Core;

abstract class AbstractController
{
    public string $layout = 'main';

    protected function setLayout(string $newLayout): static
    {
        $this->layout = $newLayout;

        return $this;
    }

    protected function renderView(string $templateName, array $params = []): View
    {
        return View::make($templateName, $params, $this->layout);
    }  
}
