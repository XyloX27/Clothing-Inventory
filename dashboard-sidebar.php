<?php if(isset($_SESSION['loggedin'])): ?>
<div class="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-tshirt me-2"></i> ClothingStock</h3>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>
        <a href="add-product.php" class="<?= basename($_SERVER['PHP_SELF']) == 'add-product.php' ? 'active' : '' ?>">
            <i class="fas fa-plus-circle me-2"></i> Add Product
        </a>
        <a href="create-order.php" class="<?= basename($_SERVER['PHP_SELF']) == 'create-order.php' ? 'active' : '' ?>">
            <i class="fas fa-cart-plus me-2"></i> Create Order
        </a>
        <a href="order-list.php" class="<?= basename($_SERVER['PHP_SELF']) == 'order-list.php' ? 'active' : '' ?>">
            <i class="fas fa-list me-2"></i> Order List
        </a>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
    </nav>
</div>
<?php endif; ?>