<?php
declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\DB;
use App\Database\Interfaces\MigrationInterface;

class m0003_add_verification_tables implements MigrationInterface
{
    const TABLE_NAMES = ["verification_tokens", "forgot_pass_tokens"];

    public function up(DB $db): void
    {
        $sql = "CREATE TABLE `{{table_name}}` (
        id INT NOT NULL AUTO_INCREMENT,
        user_id BINARY(16) NOT NULL,
        token_hash VARCHAR(64) NOT NULL UNIQUE,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        );";

        $sql_create_verification = str_replace("{{table_name}}", static::TABLE_NAMES[0], $sql);
        $sql_forgot_pass_table = str_replace("{{table_name}}", static::TABLE_NAMES[1], $sql);

        $db->exec($sql_create_verification);
        $db->exec($sql_forgot_pass_table);
    }

    public function down(DB $db): void
    {
        $db->exec("DROP TABLE " . static::TABLE_NAMES[0] . ";");
        $db->exec("DROP TABLE " . static::TABLE_NAMES[1] . ";");
    }
}
