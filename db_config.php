<?php
// Database Configuration
$host = "localhost";
$dbname = "clothing_inventory_db";
$user = "root";
$password = "";

// Absolute Path Configuration
define('BASE_DIR', realpath(__DIR__));

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8mb4'");
} catch(PDOException $e) {
    die("<div class='alert alert-danger container mt-4'>
            <h3>Database Connection Failed</h3>
            <p>Error: " . $e->getMessage() . "</p>
            <p>Config: mysql:host=$host;dbname=$dbname</p>
        </div>");
}
?>