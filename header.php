<?php 
// Session started in dashboard.php - no need to start here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clothing Inventory</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #6c5ce7 0%, #764ba2 100%);
            min-height: 100vh;
            position: fixed;
            color: white;
        }
        .sidebar-nav a {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
            body.sidebar-visible .sidebar {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="<?= isset($_SESSION['loggedin']) ? 'sidebar-visible' : '' ?>">
    
    <?php if(isset($_SESSION['loggedin'])): ?>
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <div class="sidebar-header text-center mb-4">
            <h4><i class="fas fa-tshirt me-2"></i> ClothingStock</h4>
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
            <a href="logout.php" class="mt-4">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </nav>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="main-content">