<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;
use App\Core\DB;

final class Language
{
    public static function getAll(): array
    {
        $db = Application::app()->container->get(DB::class);

        $stmt = $db->prepare("SELECT code, name FROM languages ORDER BY name;");

        $ok = $stmt->execute();

        if ($ok) {
            return $stmt->fetchAll();
        }
        return [];
    }
}
