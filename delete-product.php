<?php
require_once 'db_config.php';
session_start();

if(!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $productId = $_POST['product_id'];
        
        // Get product image path first
        $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        
        // Delete image file if exists
        if(!empty($product['image_path']) && file_exists($product['image_path'])) {
            unlink($product['image_path']);
        }
        
        $_SESSION['message'] = '<div class="alert alert-success">Product deleted successfully!</div>';
        
    } catch(PDOException $e) {
        $_SESSION['message'] = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
}

header("Location: add-product.php");
exit;