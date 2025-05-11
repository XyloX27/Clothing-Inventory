<?php
require_once 'db_config.php';
session_start();

if(!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

include 'header.php';

try {
    $orders = $conn->query("
        SELECT 
            o.id,
            o.customer_name,
            o.order_date,
            o.total_amount,
            o.status,
            GROUP_CONCAT(p.product_name SEPARATOR ', ') AS products
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        GROUP BY o.id
        ORDER BY o.order_date DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>");
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-clipboard-list me-2"></i> Order List</h2>
        <a href="create-order.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> New Order
        </a>
    </div>

    <?php if(isset($_SESSION['message'])): ?>
        <?= $_SESSION['message'] ?>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Products</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                            <td><?= htmlspecialchars($order['products'] ?? 'N/A') ?></td>
                            <td>$<?= number_format($order['total_amount'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= match($order['status']) {
                                    'completed' => 'success',
                                    'processing' => 'primary',
                                    'canceled' => 'danger',
                                    default => 'secondary'
                                } ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 align-items-center">
                                    <!-- View Button -->
                                    <a href="view-order.php?id=<?= $order['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="View Order">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="delete-order.php" method="POST" class="d-inline">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete Order"
                                                onclick="return confirm('Permanently delete this order?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                    <!-- Status Buttons -->
                                    <div class="btn-group btn-group-sm">
                                        <?php foreach(['pending', 'processing', 'completed', 'canceled'] as $status): ?>
                                            <button type="button" 
                                                class="btn btn-outline-<?= match($status) {
                                                    'pending' => 'secondary',
                                                    'processing' => 'primary',
                                                    'completed' => 'success',
                                                    'canceled' => 'danger'
                                                } ?> status-btn <?= $order['status'] === $status ? 'active' : '' ?>" 
                                                data-status="<?= $status ?>"
                                                data-order-id="<?= $order['id'] ?>"
                                                title="Mark as <?= ucfirst($status) ?>">
                                                <?= ucfirst($status) ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    // Status Update Handler
    $(document).on('click', '.status-btn', function() {
        const btn = $(this);
        const orderId = btn.data('order-id');
        const newStatus = btn.data('status');
        
        // Show loading state
        const originalText = btn.html();
        btn.html(`
            <span class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </span>
        `);

        $.ajax({
            url: 'update-order-status.php',
            method: 'POST',
            dataType: 'json',
            data: {
                order_id: orderId,
                new_status: newStatus
            },
            success: function(response) {
                if(response.status === 'success') {
                    // Update badge
                    const badge = btn.closest('tr').find('.badge');
                    badge.removeClass().addClass('badge ' + response.badge_class);
                    badge.text(response.new_status.charAt(0).toUpperCase() + response.new_status.slice(1));
                    
                    // Update button states
                    btn.closest('.btn-group').find('.status-btn').removeClass('active');
                    btn.addClass('active');
                }
            },
            error: function(xhr) {
                console.error('Status update failed:', xhr.responseText);
                alert('Error: ' + (xhr.responseJSON?.message || 'Status update failed'));
            },
            complete: function() {
                btn.html(originalText);
            }
        });
    });
});
</script>