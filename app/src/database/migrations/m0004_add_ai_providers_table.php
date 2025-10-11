<?php

use App\Core\DB;
use App\Database\Interfaces\MigrationInterface;

class m0004_add_ai_providers_table implements MigrationInterface
{
    public function up(DB $db): void
    {
        $sql = "CREATE TABLE ai_providers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        provider_key VARCHAR(50) NOT NULL UNIQUE,
        name VARCHAR(100) NOT NULL,
        api_base_url VARCHAR(255)
        );";

        $db->exec($sql);
    }

    public function down(DB $db): void
    {
        $db->exec("DROP TABLE ai_providers;");
    }
}
