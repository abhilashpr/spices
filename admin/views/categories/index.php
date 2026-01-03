<?php
$title = 'Categories';
ob_start();
?>
<div class="admin-header">
    <h1>Category Management</h1>
    <p>Manage product categories</p>
</div>

<div class="admin-panel">
    <div class="admin-form-section">
        <h3>Add New Category</h3>
        <form method="POST" action="<?= admin_url('index.php?page=categories&action=create') ?>" class="row g-3">
            <input type="hidden" name="action" value="create">
            <div class="col-md-6">
                <label for="name" class="form-label">Category Name *</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="col-md-6">
                <label for="slug" class="form-label">Slug *</label>
                <input type="text" class="form-control" id="slug" name="slug" required>
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="col-md-6">
                <label for="display_order" class="form-label">Display Order</label>
                <input type="number" class="form-control" id="display_order" name="display_order" value="0" min="0">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Create Category</button>
            </div>
        </form>
    </div>

    <div class="admin-form-section">
        <h3>All Categories</h3>
        <?php if (empty($categories)): ?>
            <div class="alert alert-info">No categories found. Create your first category above.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['id'] ?></td>
                                <td><strong><?= htmlspecialchars($category['name']) ?></strong></td>
                                <td><?= htmlspecialchars($category['slug']) ?></td>
                                <td><?= htmlspecialchars($category['description'] ?? '-') ?></td>
                                <td><?= $category['display_order'] ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= admin_url('index.php?page=categories&action=edit&id=' . $category['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form method="POST" action="<?= admin_url('index.php?page=categories&action=delete') ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
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
                        Showing <?= count($categories) ?> of <?= $pagination['total'] ?> categories
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
// Auto-generate slug from name
document.getElementById('name')?.addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (slugInput && !slugInput.dataset.edited) {
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
