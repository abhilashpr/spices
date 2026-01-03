<?php
/**
 * Product SKU Management View
 */
$title = 'Manage SKU - ' . htmlspecialchars($product['name']);
ob_start();
?>
<div class="admin-header">
    <h1>Manage SKU - <?= htmlspecialchars($product['name']) ?></h1>
    <p>Add and manage SKUs (Stock Keeping Units) for this product</p>
</div>

<?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="admin-panel">
    <!-- Add SKU Form -->
    <div class="admin-form-section">
        <h3>Add New SKU</h3>
        <form method="POST" action="" class="row g-3">
            <input type="hidden" name="action" value="create">
            
            <div class="col-md-3">
                <label for="price" class="form-label">Price ($) *</label>
                <input type="number" class="form-control" id="price" name="price" 
                       step="0.01" min="0" required placeholder="0.00">
            </div>

            <div class="col-md-3">
                <label for="offer_price" class="form-label">Offer Price ($)</label>
                <input type="number" class="form-control" id="offer_price" name="offer_price" 
                       step="0.01" min="0" placeholder="0.00">
                <div class="form-text">Leave empty if no offer</div>
            </div>

            <div class="col-md-2">
                <label for="value" class="form-label">Value *</label>
                <input type="number" class="form-control" id="value" name="value" 
                       step="0.01" min="0.01" required placeholder="1.00" value="1.00">
                <div class="form-text">Quantity amount</div>
            </div>

            <div class="col-md-2">
                <label for="unit_id" class="form-label">Unit *</label>
                <select class="form-select" id="unit_id" name="unit_id" required>
                    <option value="">Select Unit</option>
                    <?php foreach ($units as $unit): ?>
                        <option value="<?= $unit['id'] ?>">
                            <?= htmlspecialchars($unit['name']) ?> (<?= htmlspecialchars($unit['symbol']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Stock Status *</label>
                <div class="mt-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_in_stock" id="in_stock" value="1" checked>
                        <label class="form-check-label" for="in_stock">In Stock</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_in_stock" id="out_of_stock" value="0">
                        <label class="form-check-label" for="out_of_stock">Out of Stock</label>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add SKU
                </button>
                <a href="<?= admin_url('index.php?page=products') ?>" class="btn btn-secondary ms-2">
                    Back to Products
                </a>
            </div>
        </form>
    </div>

    <!-- SKU List Table -->
    <div class="admin-form-section">
        <h3>SKU List</h3>
        <?php if (empty($skus)): ?>
            <div class="alert alert-info">
                No SKUs found. Add your first SKU above.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Value</th>
                            <th>Unit</th>
                            <th>Price</th>
                            <th>Offer Price</th>
                            <th>Stock Status</th>
                            <th>Created</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($skus as $sku): ?>
                            <tr>
                                <td><?= $sku['id'] ?></td>
                                <td><strong><?= number_format($sku['value'], 2) ?></strong></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars($sku['unit_name']) ?> (<?= htmlspecialchars($sku['unit_symbol']) ?>)
                                    </span>
                                </td>
                                <td><strong>$<?= number_format($sku['price'], 2) ?></strong></td>
                                <td>
                                    <?php if (!empty($sku['offer_price'])): ?>
                                        <span class="text-success"><strong>$<?= number_format($sku['offer_price'], 2) ?></strong></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $sku['is_in_stock'] ? 'success' : 'danger' ?>">
                                        <?= $sku['is_in_stock'] ? 'In Stock' : 'Out of Stock' ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($sku['created_at'])) ?></td>
                                <td>
                                    <form method="POST" action="" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this SKU?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $sku['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

