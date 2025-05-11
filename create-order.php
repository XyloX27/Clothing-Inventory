<?php
require_once 'db_config.php';
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://");
$base_url .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$base_url = rtrim($base_url, '/');

include 'header.php';
?>

<div class="container-fluid">
    <div class="card shadow-lg">
        <div class="card-header bg-success text-white">
            <h4><i class="fas fa-cart-plus me-2"></i> Create New Order</h4>
        </div>
        <div class="card-body">
            <?php if(isset($_SESSION['message'])): ?>
                <?= $_SESSION['message'] ?>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <form id="orderForm" method="POST" action="save-order.php">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Customer Name</label>
                            <input type="text" class="form-control" name="customer_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" name="contact_number" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Delivery Address</label>
                            <textarea class="form-control" name="customer_address" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Advance Payment</label>
                            <input type="number" class="form-control" name="advance_payment" step="0.01" value="0">
                        </div>
                    </div>
                </div>

                <hr>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5><i class="fas fa-boxes me-2"></i> Order Items</h5>
                    <button type="button" class="btn btn-primary" id="addProductBtn">
                        <i class="fas fa-plus me-2"></i> Add Product
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table" id="orderItems">
                        <thead class="table-dark">
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                <td id="grandTotal">$0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Advance Paid:</strong></td>
                                <td id="advancePayment">$0.00</td>
                                <td></td>
                            </tr>
                            <tr class="table-active">
                                <td colspan="3" class="text-end"><strong>Balance Due:</strong></td>
                                <td id="balanceDue">$0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-check me-2"></i> Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Available Products</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row row-cols-1 row-cols-md-3 g-4" id="productList"></div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    const baseUrl = '<?= $base_url ?>';

    $('#addProductBtn').click(function() {
        $('#productModal').modal('show');
        loadProducts();
    });

    function loadProducts() {
        $.ajax({
            url: baseUrl + '/get_products.php',
            dataType: 'json',
            beforeSend: function() {
                $('#productList').html(`
                    <div class="col-12 text-center py-4">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
                        <p class="mt-3">Loading available products...</p>
                    </div>
                `);
            },
            success: function(response) {
                if(response.status === 'success' && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(product => {
                        html += `
                            <div class="col mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">${product.name}</h5>
                                        <p class="text-muted">$${parseFloat(product.price).toFixed(2)}</p>
                                        <p>Stock: ${product.stock}</p>
                                        <button class="btn btn-primary btn-sm select-product"
                                            data-id="${product.id}"
                                            data-name="${product.name}"
                                            data-price="${product.price}"
                                            data-stock="${product.stock}">
                                            <i class="fas fa-plus me-2"></i> Select
                                        </button>
                                    </div>
                                </div>
                            </div>`;
                    });
                    $('#productList').html(html);
                } else {
                    $('#productList').html(`
                        <div class="col-12 text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No active products available</p>
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                $('#productList').html(`
                    <div class="col-12 text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <p>Failed to load products</p>
                        <small class="text-muted">${xhr.status} - ${xhr.statusText}</small>
                    </div>
                `);
            }
        });
    }

    $(document).on('click', '.select-product', function() {
        const product = {
            id: $(this).data('id'),
            name: $(this).data('name'),
            price: parseFloat($(this).data('price')),
            stock: parseInt($(this).data('stock'))
        };

        const newRow = `
            <tr>
                <td>${product.name}<input type="hidden" name="products[]" value="${product.id}"></td>
                <td>$${product.price.toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control quantity" 
                           name="quantities[]" value="1" 
                           min="1" max="${product.stock}">
                </td>
                <td class="item-total">$${(product.price * 1).toFixed(2)}</td>
                <td>
                    <button class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>`;

        $('#orderItems tbody').append(newRow);
        $('#productModal').modal('hide');
        updateTotals();
    });

    function updateTotals() {
        let grandTotal = 0;
        
        $('.item-total').each(function() {
            const price = parseFloat($(this).closest('tr').find('td:eq(1)').text().replace('$', ''));
            const quantity = parseInt($(this).closest('tr').find('.quantity').val()) || 0;
            const total = price * quantity;
            $(this).text('$' + total.toFixed(2));
            grandTotal += total;
        });

        const advance = parseFloat($('[name="advance_payment"]').val()) || 0;
        const balance = Math.max(grandTotal - advance, 0);

        $('#grandTotal').text('$' + grandTotal.toFixed(2));
        $('#advancePayment').text('$' + advance.toFixed(2));
        $('#balanceDue').text('$' + balance.toFixed(2));
    }

    $(document).on('input', '.quantity', updateTotals);
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        updateTotals();
    });
    $('[name="advance_payment"]').on('input', updateTotals);
});
</script>