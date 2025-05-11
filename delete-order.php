<?php
require_once 'db_config.php';
session_start();

if(!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    try {
        $conn->beginTransaction();

        // Delete order items first
        $deleteItems = $conn->prepare("
            DELETE FROM order_items 
            WHERE order_id = ?
        ");
        $deleteItems->execute([$_POST['order_id']]);

        // Delete the order
        $deleteOrder = $conn->prepare("
            DELETE FROM orders 
            WHERE id = ?
        ");
        $deleteOrder->execute([$_POST['order_id']]);

        $conn->commit();
        $_SESSION['message'] = '<div class="alert alert-success">Order #'.$_POST['order_id'].' deleted successfully!</div>';

    } catch(PDOException $e) {
        $conn->rollBack();
        $_SESSION['message'] = '<div class="alert alert-danger">Error deleting order: '.$e->getMessage().'</div>';
    }
}

header("Location: order-list.php");
exit;