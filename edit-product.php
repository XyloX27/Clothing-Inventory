<?php
require_once 'db_config.php';
session_start();

if(!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

$product = [];
$message = '';

try {
    // Get product ID from URL
    $productId = $_GET['id'] ?? null;
    
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Handle form submission
        $productName = $_POST['product_name'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $description = $_POST['description'];
        
        // File upload handling
        $imagePath = $_POST['existing_image'];
        if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $targetDir = "uploads/";
            $fileName = uniqid() . '_' . basename($_FILES['product_image']['name']);
            $targetFile = $targetDir . $fileName;
            
            if(move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile)) {
                // Delete old image if exists
                if(!empty($imagePath) && file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $imagePath = $targetFile;
            }
        }

        $stmt = $conn->prepare("
            UPDATE products SET
            product_name = ?,
            price = ?,
            quantity = ?,
            description = ?,
            image_path = ?
            WHERE id = ?
        ");
        $stmt->execute([$productName, $price, $quantity, $description, $imagePath, $productId]);
        
        $message = '<div class="alert alert-success">Product updated successfully!</div>';
    }

    // Get current product data
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
}

include 'header.php';
?>

<div class="container-fluid">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4><i class="fas fa-edit me-2"></i> Edit Product</h4>
        </div>
        <div class="card-body">
            <?= $message ?>
            <form method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="product_name" 
                                   value="<?= htmlspecialchars($product['product_name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Price ($)</label>
                            <input type="number" step="0.01" class="form-control" name="price" 
                                   value="<?= htmlspecialchars($product['price'] ?? '') ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" name="quantity" 
                                   value="<?= htmlspecialchars($product['quantity'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" name="product_image">
                            <input type="hidden" name="existing_image" 
                                   value="<?= htmlspecialchars($product['image_path'] ?? '') ?>">
                            <?php if(!empty($product['image_path'])): ?>
                            <div class="mt-2">
                                <img src="<?= htmlspecialchars($product['image_path']) ?>" 
                                     style="max-width: 200px;" class="img-thumbnail">
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"><?= 
                                htmlspecialchars($product['description'] ?? '') 
                            ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Update Product
                        </button>
                        <a href="add-product.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>