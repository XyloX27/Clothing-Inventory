<link rel="stylesheet" href="style.css">

<?php
include 'db.php';
$orders = $conn->query("SELECT orders.*, products.name AS product_name FROM orders JOIN products ON orders.product_id = products.id");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['order_id'];
    $status = $_POST['status'];
    $conn->query("UPDATE orders SET status='$status' WHERE id=$id");
    header("Location: order-list.php");
}
?>
<table border="1">
<tr><th>Customer</th><th>Product</th><th>Image</th><th>Status</th><th>Actions</th></tr>
<?php while ($row = $orders->fetch_assoc()): ?>
<tr>
    <td><?= $row['customer_name'] ?></td>
    <td><?= $row['product_name'] ?></td>
    <td><img src="<?= $row['image_path'] ?>" width="80"></td>
    <td><?= $row['status'] ?></td>
    <td>
        <form method="POST">
            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
            <button name="status" value="missing">❌</button>
            <button name="status" value="pending">⏳</button>
            <button name="status" value="delivered">✅</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
