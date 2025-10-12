<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Interfaces\DataInjectorInterface;

abstract class AbstractController
{
    public string $layout = 'main';
    protected ?DataInjectorInterface $injector = null;

    protected function setLayout(string $newLayout): static
    {
        $this->layout = $newLayout;

        return $this;
    }

    protected function renderView(string $templateName, array $params = []): View
    {
        $params = array_merge($params, $this->injector?->inject() ?? []);
        return View::make($templateName, $this->layout, $params);
    } 
}
