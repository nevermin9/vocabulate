<?php
declare(strict_types=1);

namespace App\Database\Interfaces;

use App\Core\DB;

interface MigrationInterface {
    public function up(DB $db): void;
    public function down(DB $db): void;
}
