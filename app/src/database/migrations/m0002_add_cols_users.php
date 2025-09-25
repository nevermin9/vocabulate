<?php
declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\DB;
use App\Database\Interfaces\MigrationInterface;

class m0002_add_cols_users implements MigrationInterface
{
    public function up(DB $db): void
    {
        $stmt_create = $db->prepare(
            "ALTER TABLE `users`
            ADD COLUMN `verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `password_hash`,
            ADD COLUMN `premium` TINYINT(1) NOT NULL DEFAULT 0 AFTER `verified`;"
        );
        $stmt_create->execute();
        $stmt_update = $db->prepare("UPDATE `users` SET `verified`=1, `premium`=0");
        $stmt_update->execute();
    }

    public function down(DB $db): void
    {
        $sql = "ALTER TABLE `users`
        DROP COLUMN `premium`,
        DROP COLUMN `verified`;";

        $db->exec($sql);
    }

}
