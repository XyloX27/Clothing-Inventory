<link rel="stylesheet" href="style.css">

<?php
session_start();
if (!isset($_SESSION['user'])) header("Location: index.php");
?>
<h2>Welcome, <?= $_SESSION['user'] ?></h2>
<a href="add-product.php">Add Product</a> |
<a href="create-order.php">Create Order</a> |
<a href="order-list.php">Order List</a> |
<a href="logout.php">Logout</a>
