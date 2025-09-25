<?php
declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use Dotenv\Dotenv;
use App\Core\DB;
use App\Core\Config;

const MIGRATIONS_DIR = __DIR__ . "/src/database/migrations";

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$db = new DB(new Config($_ENV)->db);

$db->runMigrations(MIGRATIONS_DIR, parseMigrationArguments());

function parseMigrationArguments(): array
{
    global $argv;
    $args = $argv;
    
    array_shift($args);

    $command = 'up';
    $target = null;
    $rollbackAll = false;

    if (empty($args)) {
        return ['command' => $command];
    }

    if ($args[0] === 'rollback') {
        $command = 'rollback';
        
        if (!isset($args[1])) {
            $rollbackAll = true;
            return ['command' => $command, 'rollbackAll' => $rollbackAll];
        }

        if ($args[1] === '-t' && isset($args[2])) {
            $target = (int) $args[2];
            return ['command' => $command, 'target' => $target];
        }
    }

    // Default to 'up' if no specific command is recognized
    return ['command' => 'up'];
}



