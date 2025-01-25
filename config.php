<?php

if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// Database configuration
$servername = getenv('DB_HOST') ?: 'localhost';
$username_db = getenv('DB_USER') ?: 'root'; // default value if env not set
$password_db = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'main_db';

// Create connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Unable to connect to the database. Please try again later.");
}

// Set charset
$conn->set_charset("utf8mb4");

?>
