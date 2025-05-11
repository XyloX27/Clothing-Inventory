<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || !isset($_GET['order_id'])) {
    header("Location: index.php");
    exit;
}

$order_id = $_GET['order_id'];

try {
    // Get order details
    $stmt = $conn->prepare("
        SELECT o.*, GROUP_CONCAT(p.product_name SEPARATOR ', ') as products 
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>");
}

include 'header.php';
?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h3><i class="fas fa-check-circle me-2"></i> Order Confirmed</h3>
        </div>
        <div class="card-body">
            <h4>Thank you for your order, <?= htmlspecialchars($order['customer_name']) ?>!</h4>
            <p class="lead">Your order ID: <strong>#<?= htmlspecialchars($order_id) ?></strong></p>
            
            <div class="mt-4">
                <h5>Order Details:</h5>
                <ul>
                    <li>Products: <?= htmlspecialchars($order['products']) ?></li>
                    <li>Contact: <?= htmlspecialchars($order['contact_number']) ?></li>
                    <li>Status: <span class="badge bg-warning"><?= ucfirst($order['status']) ?></span></li>
                </ul>
            </div>
            
            <a href="dashboard.php" class="btn btn-primary mt-3">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>