<?php
declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\DB;
use App\Database\Interfaces\MigrationInterface;
 
class m0001_initial implements MigrationInterface
{
    public function up(DB $db): void
    {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS `users` (
            `id` binary(16) NOT NULL,
            `username` varchar(50) NOT NULL,
            `email` varchar(100) NOT NULL,
            `password_hash` varchar(255) NOT NULL,
            `ai_api_key` varchar(255) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `email` (`email`)
            );"
        );
        $db->exec(
            "CREATE TABLE IF NOT EXISTS `languages` (
            `code` varchar(10) NOT NULL,
            `name` varchar(50) NOT NULL,
            PRIMARY KEY (`code`)
            );"
        );
        $db->exec(
            "CREATE TABLE IF NOT EXISTS `stacks` (
            `id` int NOT NULL AUTO_INCREMENT,
            `user_id` binary(16) NOT NULL,
            `name` varchar(100) NOT NULL,
            `language_code` varchar(10) NOT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`),
            KEY `language_code` (`language_code`),
            CONSTRAINT `stacks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
            CONSTRAINT `stacks_ibfk_2` FOREIGN KEY (`language_code`) REFERENCES `languages` (`code`)
            );"
        );
        $db->exec(
            "CREATE TABLE IF NOT EXISTS `flashcards` (
            `id` int NOT NULL AUTO_INCREMENT,
            `user_id` binary(16) NOT NULL,
            `stack_id` int NOT NULL,
            `word` varchar(100) NOT NULL,
            `translation` varchar(100) NOT NULL,
            `example_usage` text,
            `example_usage_translation` text,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `stack_id` (`stack_id`),
            KEY `user_id` (`user_id`),
            CONSTRAINT `flashcards_ibfk_1` FOREIGN KEY (`stack_id`) REFERENCES `stacks` (`id`),
            CONSTRAINT `flashcards_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
            );"
        );
    }


    public function down(DB $db): void
    {
        $db->exec(
            "DROP TABLE flashcards;"
        );
        $db->exec(
            "DROP TABLE  stacks;"
        );
        $db->exec(
            "DROP TABLE languages;"
        );
        $db->exec(
            "DROP TABLE users;"
        );
    }
}
