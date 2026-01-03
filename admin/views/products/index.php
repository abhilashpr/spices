<?php
/**
 * Products Index View
 * Displays list of all products
 */
$title = 'Product Management';
ob_start();
?>
<div class="admin-header">
    <h1>Product Management</h1>
    <p>Manage your product inventory</p>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="admin-panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>All Products</h3>
        <a href="<?= admin_url('index.php?page=products&action=create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Product
        </a>
    </div>

    <?php if (empty($products)): ?>
        <div class="alert alert-info">
            No products found. <a href="<?= admin_url('index.php?page=products&action=create') ?>" class="alert-link">Create your first product</a>.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>SKU Count</th>
                            <th>Status</th>
                            <th>Stock</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td><?= htmlspecialchars($product['product_code'] ?? '-') ?></td>
                                <td>
                                    <?php if (!empty($product['main_image'])): ?>
                                    <?php 
                                    $imageUrl = get_image_url($product['main_image']);
                                    ?>
                                    <img src="<?= htmlspecialchars($imageUrl) ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid rgba(50, 198, 141, 0.2);">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= admin_url('index.php?page=products&action=detail&id=' . $product['id']) ?>" 
                                   class="text-decoration-none fw-bold"
                                   style="color: #09342a !important;">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </td>
                            <td>
                                <?php
                                    $skuCount = (int)($product['sku_count'] ?? 0);
                                    if ($skuCount === 0) {
                                        $skuBg = 'background: linear-gradient(135deg, #f44336, #c62828) !important;';
                                    } else {
                                        $skuBg = 'background: linear-gradient(135deg, #17a2b8, #138496) !important;';
                                    }
                                ?>
                                <span class="badge" style="<?= $skuBg ?>">
                                    <?= $skuCount ?> SKU<?= $skuCount != 1 ? 's' : '' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $product['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $product['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $product['is_out_of_stock'] ? 'danger' : 'success' ?>">
                                    <?= $product['is_out_of_stock'] ? 'Out of Stock' : 'In Stock' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= admin_url('index.php?page=products&action=detail&id=' . $product['id']) ?>" 
                                       class="action-btn action-btn-info" 
                                       title="Product Detail Page"
                                       data-tooltip="View Details">
                                        <span>👁️</span>
                                    </a>
                                    <a href="<?= admin_url('index.php?page=product_skus&action=manage&product_id=' . $product['id']) ?>" 
                                       class="action-btn action-btn-primary" 
                                       title="Manage SKU"
                                       data-tooltip="Manage SKU">
                                        <span>📦</span>
                                    </a>
                                    <a href="<?= admin_url('index.php?page=products&action=edit&id=' . $product['id']) ?>" 
                                       class="action-btn action-btn-secondary" 
                                       title="Edit Product"
                                       data-tooltip="Edit">
                                        <span>✏️</span>
                                    </a>
                                    <form method="POST" 
                                          action="<?= admin_url('index.php?page=products&action=delete') ?>" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                        <button type="submit" 
                                                class="action-btn action-btn-danger" 
                                                title="Delete Product"
                                                data-tooltip="Delete">
                                            <span>🗑️</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (isset($pagination) && $pagination['pages'] > 1): ?>
            <div class="mt-3">
                <?= render_pagination($pagination) ?>
                <div class="text-center text-muted small mt-2">
                    Showing <?= count($products) ?> of <?= $pagination['total'] ?> products
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

