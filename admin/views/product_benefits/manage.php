<?php
/**
 * Product Benefits Management View
 */
$title = 'Manage Product Benefits - ' . htmlspecialchars($product['name'] ?? 'Product');
ob_start();
?>
<div class="admin-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Manage Product Benefits</h1>
            <p>Add benefits for product: <strong><?= htmlspecialchars($product['name'] ?? 'Product') ?></strong></p>
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
            <h3 class="mb-0">Add New Benefit</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="benefit_type" class="form-label">Benefit Type *</label>
                        <select class="form-select" id="benefit_type" name="benefit_type" required>
                            <option value="">Select Type</option>
                            <option value="health_benefits">Health Benefits</option>
                            <option value="how_to_use">How to Use</option>
                            <option value="how_to_store">How to Store</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label for="benefit_text" class="form-label">Benefit Text *</label>
                        <textarea class="form-control" id="benefit_text" name="benefit_text" rows="3" required 
                                  placeholder="Enter the benefit point or instruction"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            ➕ Add Benefit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing Benefits List -->
    <div class="row g-4">
        <!-- Health Benefits -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">Health Benefits (<?= count($benefits['health_benefits'] ?? []) ?>)</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($benefits['health_benefits'])): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($benefits['health_benefits'] as $benefit): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold"><?= htmlspecialchars($benefit['benefit_text']) ?></div>
                                    </div>
                                    <form method="POST" action="" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this benefit?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $benefit['id'] ?>">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            🗑️
                                        </button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">No health benefits added yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- How to Use -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="mb-0">How to Use (<?= count($benefits['how_to_use'] ?? []) ?>)</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($benefits['how_to_use'])): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($benefits['how_to_use'] as $benefit): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold"><?= htmlspecialchars($benefit['benefit_text']) ?></div>
                                    </div>
                                    <form method="POST" action="" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this benefit?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $benefit['id'] ?>">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            🗑️
                                        </button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">No usage instructions added yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- How to Store -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0">How to Store (<?= count($benefits['how_to_store'] ?? []) ?>)</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($benefits['how_to_store'])): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($benefits['how_to_store'] as $benefit): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold"><?= htmlspecialchars($benefit['benefit_text']) ?></div>
                                    </div>
                                    <form method="POST" action="" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this benefit?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $benefit['id'] ?>">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            🗑️
                                        </button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">No storage instructions added yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

