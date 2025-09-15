<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;

final class Language
{
    public static function getAll(): array
    {
        $db = Application::db();

        $stmt = $db->prepare("SELECT code, name FROM languages ORDER BY name;");

        $ok = $stmt->execute();

        if ($ok) {
            return $stmt->fetchAll();
        }
        return [];
    }
}
