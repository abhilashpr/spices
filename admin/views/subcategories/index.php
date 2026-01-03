<?php
$title = 'Subcategories';
ob_start();
?>
<div class="admin-header">
    <h1>Subcategory Management</h1>
    <p>Manage product subcategories</p>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="admin-panel">
    <div class="admin-form-section">
        <h3>Add New Subcategory</h3>
        <form method="POST" action="<?= admin_url('index.php?page=subcategories&action=create') ?>" class="row g-3">
            <input type="hidden" name="action" value="create">
            <div class="col-md-6">
                <label for="category_id" class="form-label">Parent Category *</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="name" class="form-label">Subcategory Name *</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="col-md-6">
                <label for="slug" class="form-label">Slug *</label>
                <input type="text" class="form-control" id="slug" name="slug" required>
            </div>
            <div class="col-md-6">
                <label for="display_order" class="form-label">Display Order</label>
                <input type="number" class="form-control" id="display_order" name="display_order" value="0" min="0">
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Create Subcategory</button>
            </div>
        </form>
    </div>

    <div class="admin-form-section">
        <h3>All Subcategories</h3>
        <?php if (empty($subcategories)): ?>
            <div class="alert alert-info">No subcategories found. Create your first subcategory above.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subcategories as $subcategory): ?>
                            <tr>
                                <td><?= $subcategory['id'] ?></td>
                                <td><strong><?= htmlspecialchars($subcategory['category_name'] ?? '-') ?></strong></td>
                                <td><?= htmlspecialchars($subcategory['name']) ?></td>
                                <td><?= htmlspecialchars($subcategory['slug']) ?></td>
                                <td><?= htmlspecialchars($subcategory['description'] ?? '-') ?></td>
                                <td><?= $subcategory['display_order'] ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= admin_url('index.php?page=subcategories&action=edit&id=' . $subcategory['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form method="POST" action="<?= admin_url('index.php?page=subcategories&action=delete') ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this subcategory?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $subcategory['id'] ?>">
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
                        Showing <?= count($subcategories) ?> of <?= $pagination['total'] ?> subcategories
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
