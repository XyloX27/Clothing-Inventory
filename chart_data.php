<?php
require_once 'db_config.php';
header('Content-Type: application/json');

try {
    // Product Availability Data
    $availability = $conn->query("
        SELECT 
            CASE 
                WHEN quantity > 10 THEN 'High'
                WHEN quantity BETWEEN 5 AND 10 THEN 'Medium' 
                ELSE 'Low'
            END AS status,
            COUNT(*) AS count
        FROM products
        GROUP BY status
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Revenue Data
    $revenue = $conn->query("
        SELECT 
            DATE_FORMAT(order_date, '%Y-%m') AS month,
            SUM(total_amount) AS total
        FROM orders
        GROUP BY DATE_FORMAT(order_date, '%Y-%m')
        ORDER BY order_date DESC
        LIMIT 6
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Product Demand Data
    $demand = $conn->query("
        SELECT p.product_name, SUM(oi.quantity) AS total_sold
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        GROUP BY p.product_name
        ORDER BY total_sold DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'availability' => $availability,
        'revenue' => $revenue,
        'demand' => $demand
    ]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}