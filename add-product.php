<?php
require_once 'db_config.php';
session_start();
if(!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

$message = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $productName = $_POST['product_name'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $description = $_POST['description'];
        
        // File upload handling
        $imagePath = null;
        if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $targetDir = "uploads/";
            $fileName = uniqid() . '_' . basename($_FILES['product_image']['name']);
            $targetFile = $targetDir . $fileName;
            
            if(move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile;
            }
        }

        $stmt = $conn->prepare("
            INSERT INTO products 
            (product_name, price, quantity, description, image_path)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$productName, $price, $quantity, $description, $imagePath]);
        
        $message = '<div class="alert alert-success">Product added successfully!</div>';
        
    } catch(PDOException $e) {
        $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
}

try {
    $products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $message = '<div class="alert alert-danger">Error loading products: ' . $e->getMessage() . '</div>';
}

include 'header.php';
?>

<div class="container-fluid">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4><i class="fas fa-plus-circle me-2"></i> Add New Product</h4>
        </div>
        <div class="card-body">
            <?= $message ?>
            
            <!-- Add Product Form -->
            <form method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="product_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Price ($)</label>
                            <input type="number" step="0.01" class="form-control" name="price" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" name="quantity" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" name="product_image">
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Save Product
                        </button>
                    </div>
                </div>
            </form>

            <hr class="my-5">

            <!-- Existing Products List -->
            <h4 class="mb-3"><i class="fas fa-boxes me-2"></i> Existing Products</h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['product_name'] ?? 'N/A') ?></td>
                            <td>$<?= number_format($product['price'] ?? 0, 2) ?></td>
                            <td><?= $product['quantity'] ?? 0 ?></td>
                            <td>
                                <?php if(!empty($product['image_path'])): ?>
                                <img src="<?= htmlspecialchars($product['image_path']) ?>" 
                                     alt="Product Image" 
                                     style="max-width: 80px;">
                                <?php else: ?>
                                <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit-product.php?id=<?= $product['id'] ?>" 
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="delete-product.php" method="POST" class="d-inline">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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