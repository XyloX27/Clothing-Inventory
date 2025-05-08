<link rel="stylesheet" href="style.css">

<?php
include 'db.php';
$products = $conn->query("SELECT * FROM products");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cname = $_POST['customer_name'];
    $caddr = $_POST['customer_address'];
    $cnum  = $_POST['customer_number'];
    $pid   = $_POST['product_id'];

    $img = $_FILES['product_image'];
    $target = "uploads/" . basename($img['name']);
    move_uploaded_file($img['tmp_name'], $target);

    $conn->query("INSERT INTO orders (customer_name, customer_address, customer_number, product_id, image_path) 
        VALUES ('$cname', '$caddr', '$cnum', $pid, '$target')");
    echo "Order submitted!";
}
?>
<form method="POST" enctype="multipart/form-data">
    <input name="customer_name" required placeholder="Customer Name">
    <input name="customer_address" required placeholder="Address">
    <input name="customer_number" required placeholder="Phone">
    <select name="product_id">
        <?php while ($row = $products->fetch_assoc()) echo "<option value='{$row['id']}'>{$row['name']} - \${$row['price']}</option>"; ?>
    </select>
    <input type="file" name="product_image" required>
    <button type="submit">Submit Order</button>
</form>
