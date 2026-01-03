<?php
/**
 * Unit Form View
 * Used for editing units
 */
$title = ($unit ? 'Edit' : 'Add') . ' Unit';
ob_start();
?>
<div class="admin-header">
    <h1><?= $unit ? 'Edit Unit' : 'Add New Unit' ?></h1>
    <p><?= $unit ? 'Update unit information' : 'Create a new measurement unit' ?></p>
</div>

<?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="admin-panel">
    <form method="POST" action="" class="row g-3">
        <?php if ($unit): ?>
            <input type="hidden" name="id" value="<?= $unit['id'] ?>">
        <?php endif; ?>

        <div class="col-md-6">
            <label for="name" class="form-label">Unit Name *</label>
            <input type="text" class="form-control" id="name" name="name" 
                   value="<?= htmlspecialchars($unit['name'] ?? '') ?>" required
                   placeholder="e.g., Kilogram, Gram, Piece">
        </div>

        <div class="col-md-6">
            <label for="symbol" class="form-label">Symbol *</label>
            <input type="text" class="form-control" id="symbol" name="symbol" 
                   value="<?= htmlspecialchars($unit['symbol'] ?? '') ?>" required
                   placeholder="e.g., kg, g, nos" maxlength="20">
            <div class="form-text">Short symbol/abbreviation for the unit</div>
        </div>

        <div class="col-md-6">
            <label for="display_order" class="form-label">Display Order</label>
            <input type="number" class="form-control" id="display_order" name="display_order" 
                   value="<?= $unit['display_order'] ?? 0 ?>" min="0">
            <div class="form-text">Lower numbers appear first</div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Status</label>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" 
                       <?= (!isset($unit) || $unit['is_active']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_active">
                    Active (visible in dropdowns)
                </label>
            </div>
        </div>

        <div class="col-12">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($unit['description'] ?? '') ?></textarea>
            <div class="form-text">Optional description for this unit</div>
        </div>

        <div class="col-12 border-top pt-3">
            <button type="submit" class="btn btn-primary btn-lg">
                <?= $unit ? 'Update Unit' : 'Create Unit' ?>
            </button>
            <a href="<?= admin_url('index.php?page=units') ?>" class="btn btn-secondary btn-lg ms-2">
                Cancel
            </a>
        </div>
    </form>
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

