<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * CLI Database Import Utility
 * 
 * Usage: php scripts/import_db.php
 * Imports database/averroes-ltd.sql into the configured database
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("Direct web access forbidden. Run via CLI: php scripts/import_db.php\n");
}

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/db.php';

$schemaFile = dirname(__DIR__) . '/database/averroes-ltd.sql';
if (!file_exists($schemaFile) || !is_readable($schemaFile)) {
    fwrite(STDERR, "Error: database file not found at database/averroes-ltd.sql\n");
    exit(1);
}

try {
    $pdo = get_db(true);
    if (!$pdo) {
        fwrite(STDERR, "Error: Could not connect to database.\n");
        exit(1);
    }

    echo "Connected to database successfully.\n";
    echo "Importing schema from " . basename($schemaFile) . "...\n";

    $sql = file_get_contents($schemaFile);
    $pdo->exec($sql);

    // Verify imported tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ Database schema imported successfully (" . count($tables) . " tables verified):\n";
    foreach ($tables as $t) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        echo "   - $t ($count rows)\n";
    }

} catch (Throwable $e) {
    fwrite(STDERR, "Database Import Error: " . $e->getMessage() . "\n");
    exit(1);
}
