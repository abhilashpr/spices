<?php
$title = ($subcategory ? 'Edit' : 'Add') . ' Subcategory';
ob_start();
?>
<div class="admin-header">
    <h1><?= $subcategory ? 'Edit Subcategory' : 'Add New Subcategory' ?></h1>
</div>

<div class="admin-panel">
    <form method="POST" action="">
        <input type="hidden" name="action" value="<?= $subcategory ? 'update' : 'create' ?>">
        <?php if ($subcategory): ?>
            <input type="hidden" name="id" value="<?= $subcategory['id'] ?>">
        <?php endif; ?>
        <div class="admin-form-row">
            <div class="admin-form-group">
                <label for="category_id">Parent Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" 
                                <?= ($subcategory && $subcategory['category_id'] == $category['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="admin-form-group">
                <label for="name">Subcategory Name *</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($subcategory['name'] ?? '') ?>" required>
            </div>
        </div>
        <div class="admin-form-row">
            <div class="admin-form-group">
                <label for="slug">Slug *</label>
                <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($subcategory['slug'] ?? '') ?>" required>
            </div>
            <div class="admin-form-group">
                <label for="display_order">Display Order</label>
                <input type="number" id="display_order" name="display_order" value="<?= $subcategory['display_order'] ?? 0 ?>" min="0">
            </div>
        </div>
        <div class="admin-form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?= htmlspecialchars($subcategory['description'] ?? '') ?></textarea>
        </div>
        <div>
            <button type="submit" class="admin-btn admin-btn-primary"><?= $subcategory ? 'Update Subcategory' : 'Create Subcategory' ?></button>
            <a href="<?= admin_url('index.php?page=subcategories') ?>" class="admin-btn admin-btn-secondary" style="text-decoration: none; display: inline-block; margin-left: 12px;">Cancel</a>
        </div>
    </form>
</div>

<script>
// Auto-generate slug from name
document.getElementById('name')?.addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (slugInput && !slugInput.dataset.edited && !<?= $subcategory ? 'true' : 'false' ?>) {
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

