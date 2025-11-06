<?php
/**
 * Database Configuration
 * Koneksi ke MySQL menggunakan XAMPP
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // Default XAMPP username
define('DB_PASS', '');               // Default XAMPP password (kosong)
define('DB_NAME', 'anime_streaming');

// Timezone setting
date_default_timezone_set('Asia/Jakarta');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8 untuk support karakter Japanese
$conn->set_charset("utf8mb4");

/**
 * Function untuk execute query dengan prepared statement
 * @param object $conn - Database connection
 * @param string $query - SQL query
 * @param array $params - Parameters untuk bind
 * @param string $types - Tipe data parameters (s=string, i=integer, d=double)
 * @return object - Result object
 */
function executeQuery($conn, $query, $params = [], $types = "") {
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    
    return $stmt;
}

/**
 * Function untuk fetch semua data
 * @param object $stmt - Statement object
 * @return array - Array of results
 */
function fetchAll($stmt) {
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Function untuk fetch single row
 * @param object $stmt - Statement object
 * @return array - Single row result
 */
function fetchOne($stmt) {
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Function untuk sanitize input
 * @param object $conn - Database connection
 * @param string $data - Data to sanitize
 * @return string - Sanitized data
 */
function sanitize($conn, $data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Test connection (optional - comment out di production)
// echo "Database connected successfully!";
?>