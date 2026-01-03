<?php
/**
 * Units Index View
 * Displays list of all units
 */
$title = 'Unit Management';
ob_start();
?>
<div class="admin-header">
    <h1>Unit Management</h1>
    <p>Manage measurement units (kg, gram, nos, etc.)</p>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="admin-panel">
    <div class="admin-form-section">
        <h3>Add New Unit</h3>
        <form method="POST" action="<?= admin_url('index.php?page=units&action=create') ?>" class="row g-3">
            <div class="col-md-4">
                <label for="name" class="form-label">Unit Name *</label>
                <input type="text" class="form-control" id="name" name="name" required 
                       placeholder="e.g., Kilogram, Gram, Piece">
            </div>
            <div class="col-md-3">
                <label for="symbol" class="form-label">Symbol *</label>
                <input type="text" class="form-control" id="symbol" name="symbol" required 
                       placeholder="e.g., kg, g, nos" maxlength="20">
            </div>
            <div class="col-md-3">
                <label for="display_order" class="form-label">Display Order</label>
                <input type="number" class="form-control" id="display_order" name="display_order" 
                       value="0" min="0">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="2"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Add Unit</button>
            </div>
        </form>
    </div>

    <div class="admin-form-section">
        <h3>All Units</h3>
        <?php if (empty($units)): ?>
            <div class="alert alert-info">
                No units found. Add your first unit above.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Symbol</th>
                            <th>Description</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($units as $unit): ?>
                            <tr>
                                <td><?= $unit['id'] ?></td>
                                <td><strong><?= htmlspecialchars($unit['name']) ?></strong></td>
                                <td><code><?= htmlspecialchars($unit['symbol']) ?></code></td>
                                <td><?= htmlspecialchars($unit['description'] ?? '-') ?></td>
                                <td><?= $unit['display_order'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $unit['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $unit['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= admin_url('index.php?page=units&action=edit&id=' . $unit['id']) ?>" 
                                           class="btn btn-outline-secondary">Edit</a>
                                        <form method="POST" 
                                              action="<?= admin_url('index.php?page=units&action=toggleActive') ?>" 
                                              style="display: inline;">
                                            <input type="hidden" name="id" value="<?= $unit['id'] ?>">
                                            <button type="submit" 
                                                    class="btn btn-outline-<?= $unit['is_active'] ? 'warning' : 'success' ?>"
                                                    title="<?= $unit['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                                <?= $unit['is_active'] ? 'Deactivate' : 'Activate' ?>
                                            </button>
                                        </form>
                                        <form method="POST" 
                                              action="<?= admin_url('index.php?page=units&action=delete') ?>" 
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this unit?');">
                                            <input type="hidden" name="id" value="<?= $unit['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Auto-uppercase symbol input
document.getElementById('symbol')?.addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

