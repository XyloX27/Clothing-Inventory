<link rel="stylesheet" href="style.css">

<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $conn->query("INSERT INTO products (name, price) VALUES ('$name', '$price')");
    echo "Product added!";
}
?>
<form method="POST">
    <input name="name" placeholder="Product Name" required>
    <input name="price" type="number" step="0.01" placeholder="Price" required>
    <button type="submit">Add</button>
</form>
