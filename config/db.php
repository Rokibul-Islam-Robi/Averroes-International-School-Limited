<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * Secure PDO Database Connection
 */

require_once __DIR__ . '/config.php';

/**
 * Get or create the singleton PDO connection
 *
 * @throws PDOException if connection fails and throwOnError is true
 * @return PDO|null
 */
function get_db(bool $throwOnError = false): ?PDO {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $databasesToTry = array_unique([DB_NAME, 'database', 'averroes_db']);

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci",
    ];

    // Optional SSL CA Certificate (for PlanetScale, TiDB, AWS RDS, Supabase)
    $sslCa = env('DB_SSL_CA', env('MYSQL_ATTR_SSL_CA'));
    if (!empty($sslCa) && is_string($sslCa) && file_exists($sslCa)) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
    }
    if (env('DB_SSL_VERIFY') === false) {
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    }

    $lastException = null;

    foreach ($databasesToTry as $dbName) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            $dbName,
            DB_CHARSET
        );

        try {
            $testPdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            // Verify if tables exist in this database
            $tables = $testPdo->query("SHOW TABLES")->fetchAll();
            if (!empty($tables)) {
                $pdo = $testPdo;
                return $pdo;
            }
            if ($pdo === null) {
                $pdo = $testPdo;
            }
        } catch (PDOException $e) {
            $lastException = $e;
        }
    }

    if ($pdo !== null) {
        return $pdo;
    }

    if ($lastException !== null) {
        error_log('[Averroes DB Connection Error] ' . $lastException->getMessage());
        if ($throwOnError) {
            throw $lastException;
        }
    }

    return null;
}

/**
 * Test if database connection is alive and initialized
 */
function is_db_connected(): bool {
    try {
        $db = get_db(false);
        if ($db === null) {
            return false;
        }
        $stmt = $db->query("SELECT 1");
        return (bool)$stmt;
    } catch (Throwable $e) {
        return false;
    }
}
