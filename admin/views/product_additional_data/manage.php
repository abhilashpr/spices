<?php
/**
 * Product Additional Data Management View
 */
$title = 'Manage Additional Data - ' . htmlspecialchars($product['name'] ?? 'Product');
ob_start();
?>
<div class="admin-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Manage Additional Data</h1>
            <p>Add key-value pairs for product: <strong><?= htmlspecialchars($product['name'] ?? 'Product') ?></strong></p>
        </div>
        <div>
            <a href="<?= admin_url('index.php?page=products&action=detail&id=' . ($product['id'] ?? 0)) ?>" class="btn btn-secondary">
                ← Back to Product Detail
            </a>
        </div>
    </div>
</div>

<?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="admin-panel">
    <!-- Add Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="mb-0">Add New Additional Data</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="data_key" class="form-label">Key *</label>
                        <input type="text" class="form-control" id="data_key" name="data_key" required 
                               placeholder="e.g., Weight, Dimensions, Origin">
                    </div>
                    <div class="col-md-6">
                        <label for="data_value" class="form-label">Value *</label>
                        <input type="text" class="form-control" id="data_value" name="data_value" required 
                               placeholder="e.g., 500g, 10x10x5 cm, India">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            ➕ Add Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing Data List -->
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Existing Additional Data (<?= count($additionalData ?? []) ?>)</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($additionalData)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($additionalData as $data): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($data['data_key']) ?></strong></td>
                                    <td><?= htmlspecialchars($data['data_value']) ?></td>
                                    <td>
                                        <form method="POST" action="" 
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this data?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $data['id'] ?>">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No additional data added yet. Use the form above to add key-value pairs.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

