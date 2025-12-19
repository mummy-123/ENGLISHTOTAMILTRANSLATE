<?php
// -----------------------------
// config/db.php
// -----------------------------

// Show errors during development (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set PHP timezone (IMPORTANT for OTP expiry)
date_default_timezone_set('Asia/Kolkata');

// JSON + CORS headers
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle CORS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// DB config
$DB_HOST = '127.0.0.1';
$DB_NAME = 'tamil_translator';
$DB_USER = 'root';
$DB_PASS = ''; // XAMPP default

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Set MySQL timezone (IMPORTANT)
    $pdo->exec("SET time_zone = '+05:30'");

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'DB connection failed'
    ]);
    exit;
}