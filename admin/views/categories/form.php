<?php
$title = ($category ? 'Edit' : 'Add') . ' Category';
ob_start();
?>
<div class="admin-header">
    <h1><?= $category ? 'Edit Category' : 'Add New Category' ?></h1>
</div>

<div class="admin-panel">
    <form method="POST" action="">
        <input type="hidden" name="action" value="<?= $category ? 'update' : 'create' ?>">
        <?php if ($category): ?>
            <input type="hidden" name="id" value="<?= $category['id'] ?>">
        <?php endif; ?>
        <div class="admin-form-row">
            <div class="admin-form-group">
                <label for="name">Category Name *</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($category['name'] ?? '') ?>" required>
            </div>
            <div class="admin-form-group">
                <label for="slug">Slug *</label>
                <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($category['slug'] ?? '') ?>" required>
            </div>
        </div>
        <div class="admin-form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
        </div>
        <div class="admin-form-group">
            <label for="display_order">Display Order</label>
            <input type="number" id="display_order" name="display_order" value="<?= $category['display_order'] ?? 0 ?>" min="0">
        </div>
        <div>
            <button type="submit" class="admin-btn admin-btn-primary"><?= $category ? 'Update Category' : 'Create Category' ?></button>
            <a href="<?= admin_url('index.php?page=categories') ?>" class="admin-btn admin-btn-secondary" style="text-decoration: none; display: inline-block; margin-left: 12px;">Cancel</a>
        </div>
    </form>
</div>

<script>
// Auto-generate slug from name
document.getElementById('name')?.addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (slugInput && !slugInput.dataset.edited && !<?= $category ? 'true' : 'false' ?>) {
        const slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
    }
});

document.getElementById('slug')?.addEventListener('input', function() {
    this.dataset.edited = 'true';
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

