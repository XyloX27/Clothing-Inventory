<?php
header("Content-Type: application/json");
require_once 'db_config.php';

try {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->query("
        SELECT 
            id, 
            product_name AS name, 
            price, 
            quantity AS stock 
        FROM products 
        WHERE status = 'active'
    ");

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(
        [
            'status' => 'success',
            'data' => $products
        ],
        JSON_HEX_APOS | JSON_UNESCAPED_UNICODE
    );

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}