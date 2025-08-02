<?php
/**
 * Database Configuration for Ibrae Portfolio
 * MySQL connection settings for XAMPP
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ibrae_portfolio');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP MySQL password is empty
define('DB_CHARSET', 'utf8mb4');

try {
    // Create PDO connection
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
    // Test connection
    $pdo->query("SELECT 1");
    
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    
    // Send JSON error response for API calls
    if (isset($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] === 'application/json') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed. Please check your configuration.',
            'error' => 'DB_CONNECTION_ERROR',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    // For regular page loads, show user-friendly error
    die('Database connection failed. Please ensure XAMPP MySQL is running and the database is set up correctly.');
}
?>
