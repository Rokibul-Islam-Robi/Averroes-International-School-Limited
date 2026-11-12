<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * CLI Database Export Utility
 * 
 * Usage: php scripts/export_db.php
 * Exports the live database into database/averroes-ltd.sql
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("Direct web access forbidden. Run via CLI: php scripts/export_db.php\n");
}

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/db.php';

try {
    $pdo = get_db(true);
    if (!$pdo) {
        fwrite(STDERR, "Error: Could not connect to database.\n");
        exit(1);
    }

    $targetFile = dirname(__DIR__) . '/database/averroes-ltd.sql';
    
    $output = "-- ====================================================================\n";
    $output .= "-- Averroes International School CMS - Complete Unified Database Dump\n";
    $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $output .= "-- Target Server: MySQL 5.7+ / MySQL 8.0+ / MariaDB / PlanetScale\n";
    $output .= "-- To create a new database manually, you can run:\n";
    $output .= "--   CREATE DATABASE IF NOT EXISTS `averroes_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
    $output .= "--   USE `averroes_db`;\n";
    $output .= "-- ====================================================================\n\n";
    $output .= "SET FOREIGN_KEY_CHECKS = 0;\n";
    $output .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
    $output .= "SET time_zone = '+00:00';\n";
    $output .= "SET NAMES utf8mb4;\n\n";

    $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_NUM);

    foreach ($tables as $tRow) {
        $table = $tRow[0];

        $output .= "-- --------------------------------------------------------------------\n";
        $output .= "-- Table structure for `$table`\n";
        $output .= "-- --------------------------------------------------------------------\n";
        $output .= "DROP TABLE IF EXISTS `$table`;\n";

        $createRow = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        $createSql = $createRow['Create Table'] ?? '';
        $output .= $createSql . ";\n\n";

        // Dump data
        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            $output .= "-- Dumping data for table `$table`\n";
            $columns = array_keys($rows[0]);
            $colList = implode('`, `', $columns);

            $chunks = array_chunk($rows, 50);
            foreach ($chunks as $chunk) {
                $output .= "INSERT INTO `$table` (`$colList`) VALUES\n";
                $valRows = [];
                foreach ($chunk as $row) {
                    $vals = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $vals[] = 'NULL';
                        } else {
                            $vals[] = $pdo->quote((string)$val);
                        }
                    }
                    $valRows[] = '(' . implode(', ', $vals) . ')';
                }
                $output .= implode(",\n", $valRows) . ";\n";
            }
            $output .= "\n";
        }
    }

    $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

    file_put_contents($targetFile, $output);
    echo "✓ Database dump successfully exported to database/averroes-ltd.sql (" . number_format(strlen($output)) . " bytes, " . count($tables) . " tables).\n";

} catch (Throwable $e) {
    fwrite(STDERR, "Database Dump Error: " . $e->getMessage() . "\n");
    exit(1);
}
