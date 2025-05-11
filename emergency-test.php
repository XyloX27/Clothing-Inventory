<?php
header("Content-Type: text/plain");
require_once 'db_config.php';

try {
    // Test 1: Connection check
    echo "=== DATABASE CONNECTION TEST ===\n";
    $conn->query("SELECT 1");
    echo "Connection successful!\n";

    // Test 2: Table structure check
    echo "\n=== TABLE STRUCTURE TEST ===\n";
    $stmt = $conn->query("DESCRIBE products");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    print_r($columns);

    // Test 3: Product data check
    echo "\n=== PRODUCT DATA TEST ===\n";
    $stmt = $conn->query("SELECT id, product_name, status FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($products);

} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage();
}