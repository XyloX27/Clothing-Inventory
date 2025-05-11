<?php
session_start();
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

try {
    // Process order data
    $customer_name = htmlspecialchars($_POST['customer_name']);
    $contact_number = htmlspecialchars($_POST['contact_number']);
    $products = $_POST['products']; // Array of product IDs
    
    // Insert order
    $stmt = $conn->prepare("
        INSERT INTO orders (customer_name, contact_number, status) 
        VALUES (?, ?, 'processing')
    ");
    $stmt->execute([$customer_name, $contact_number]);
    $order_id = $conn->lastInsertId();
    
    // Insert order items
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id) 
        VALUES (?, ?)
    ");
    
    foreach ($products as $product_id) {
        $stmt->execute([$order_id, $product_id]);
    }
    
    // Redirect to confirmation
    header("Location: order_confirmation.php?order_id=" . $order_id);
    exit;

} catch(PDOException $e) {
    die("<div class='alert alert-danger'>Order failed: " . $e->getMessage() . "</div>");
}