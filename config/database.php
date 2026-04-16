<?php

/**
 * Database Configuration File
 * Centralized database connection configuration
 */

// Database Constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'test');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application Constants
define('UPLOAD_DIR', __DIR__ . '/../images/');
define('COOKIE_EXPIRY_DAYS', 30);
define('COOKIE_EXPIRY_SECONDS', 86400 * COOKIE_EXPIRY_DAYS);

/**
 * Get database connection
 *
 * @return PDO Database connection instance
 * @throws PDOException If connection fails
 */
function get_database_connection()
{
    try {
        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=%s",
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        throw new Exception("Unable to connect to database");
    }
}
