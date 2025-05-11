<?php
session_start();
require_once 'db_config.php';

if(!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

try {
    // Existing counts
    $products_count = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $orders_count = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    
    // Existing recent orders
    $recent_orders = $conn->query("
        SELECT o.id, o.customer_name, o.contact_number, 
               o.status, p.product_name 
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        ORDER BY o.order_date DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // New data for charts
    // Monthly Sales Data (last 6 months)
    $monthly_sales = $conn->query("
        SELECT DATE_FORMAT(order_date, '%Y-%m') as month, 
               SUM(total_amount) as total 
        FROM orders 
        GROUP BY month 
        ORDER BY month DESC 
        LIMIT 6
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Order Status Distribution
    $status_counts = $conn->query("
        SELECT status, COUNT(*) as count 
        FROM orders 
        GROUP BY status
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>");
}

include 'header.php';
?>

<!-- Add Chart.js in header or here -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* NEW CSS ADDED FOR CHART SIZING */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    #salesChart, #statusChart {
        width: 100% !important;
        height: 100% !important;
    }
</style>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</h2>
    
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text display-4"><?= htmlspecialchars($products_count) ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text display-4"><?= htmlspecialchars($orders_count) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- New Charts Section -->
    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-chart-line me-2"></i> Sales Trend (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <!-- CHART CONTAINER ADDED -->
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5><i class="fas fa-chart-pie me-2"></i> Order Status Distribution</h5>
                </div>
                <div class="card-body">
                    <!-- CHART CONTAINER ADDED -->
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing Recent Orders Table -->
    <div class="card shadow mt-4">
        <div class="card-header bg-dark text-white">
            <h5><i class="fas fa-history me-2"></i> Recent Orders</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Product</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <td><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($order['contact_number'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($order['product_name'] ?? 'Multiple Products') ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    strtolower($order['status']) == 'completed' ? 'success' : 
                                    (strtolower($order['status']) == 'processing' ? 'warning' : 'secondary')
                                ?>">
                                    <?= htmlspecialchars(ucfirst($order['status'] ?? 'Pending')) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Sales Chart - UPDATED CONFIGURATION
const salesData = {
    labels: <?= json_encode(array_column(array_reverse($monthly_sales), 'month')) ?>,
    datasets: [{
        label: 'Monthly Sales',
        data: <?= json_encode(array_column(array_reverse($monthly_sales), 'total')) ?>,
        borderColor: '#4e73df',
        tension: 0.3,
        fill: true,
        backgroundColor: 'rgba(78, 115, 223, 0.05)'
    }]
};

new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: salesData,
    options: {
        responsive: true,
        maintainAspectRatio: false, // IMPORTANT FIX
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Status Chart
const statusData = {
    labels: <?= json_encode(array_column($status_counts, 'status')) ?>,
    datasets: [{
        data: <?= json_encode(array_column($status_counts, 'count')) ?>,
        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
    }]
};

new Chart(document.getElementById('statusChart'), {
    type: 'pie',
    data: statusData,
    options: {
        responsive: true,
        maintainAspectRatio: false, // ADDED FOR CONSISTENCY
        plugins: {
            legend: {
                position: 'top'
            }
        }
    }
});
</script>

<?php include 'footer.php'; ?>