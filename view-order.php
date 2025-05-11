<?php
require_once 'db_config.php';
session_start();

if(!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

if(!isset($_GET['id'])) {
    header("Location: order-list.php");
    exit;
}

try {
    // Get order details
    $orderStmt = $conn->prepare("
        SELECT * FROM orders 
        WHERE id = ?
    ");
    $orderStmt->execute([$_GET['id']]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    // Get order items
    $itemsStmt = $conn->prepare("
        SELECT p.product_name, oi.quantity, oi.unit_price 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $itemsStmt->execute([$_GET['id']]);
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>");
}

include 'header.php';
?>

<div class="container-fluid">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4><i class="fas fa-file-invoice me-2"></i> Order Details #<?= $order['id'] ?></h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Customer Information</h5>
                    <dl class="row">
                        <dt class="col-sm-4">Customer Name</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($order['customer_name']) ?></dd>

                        <dt class="col-sm-4">Contact Number</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($order['contact_number']) ?></dd>

                        <dt class="col-sm-4">Delivery Address</dt>
                        <dd class="col-sm-8"><?= nl2br(htmlspecialchars($order['customer_address'])) ?></dd>
                    </dl>
                </div>

                <div class="col-md-6">
                    <h5>Order Summary</h5>
                    <dl class="row">
                        <dt class="col-sm-4">Order Date</dt>
                        <dd class="col-sm-8"><?= date('M d, Y H:i', strtotime($order['order_date'])) ?></dd>

                        <dt class="col-sm-4">Total Amount</dt>
                        <dd class="col-sm-8">$<?= number_format($order['total_amount'], 2) ?></dd>

                        <dt class="col-sm-4">Advance Paid</dt>
                        <dd class="col-sm-8">$<?= number_format($order['advance_payment'], 2) ?></dd>

                        <dt class="col-sm-4">Balance Due</dt>
                        <dd class="col-sm-8">$<?= number_format($order['total_amount'] - $order['advance_payment'], 2) ?></dd>
                    </dl>
                </div>
            </div>

            <hr>

            <h5 class="mb-4">Order Items</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td>$<?= number_format($item['unit_price'], 2) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>$<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-end mt-4">
                <a href="order-list.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>