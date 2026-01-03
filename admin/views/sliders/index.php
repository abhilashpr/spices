<?php
/**
 * Sliders Index View
 * Displays list of all sliders with create form
 */
$title = 'Slider Management';
ob_start();
?>
<div class="admin-header">
    <h1>Slider Management</h1>
    <p>Manage hero sliders for your website</p>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="admin-panel">
    <div class="admin-form-section">
        <h3>Add New Slider</h3>
        <form method="POST" action="<?= admin_url('index.php?page=sliders&action=create') ?>" 
              id="slider-create-form" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="action" value="create">
            
            <div class="col-md-6">
                <label for="title" class="form-label">Slider Title *</label>
                <input type="text" class="form-control" id="title" name="title" required 
                       placeholder="Enter slider title">
            </div>
            <div class="col-md-6">
                <label for="slider_type" class="form-label">Slider Type *</label>
                <select class="form-select" id="slider_type" name="slider_type" required>
                    <option value="static">Static Slider</option>
                    <option value="link">Slider with Link</option>
                </select>
            </div>

            <div class="col-12">
                <label for="image" class="form-label">Slider Image *</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" required>
                <div class="form-text">Upload an image file (JPEG, PNG, GIF, or WebP). Maximum size: 5MB</div>
                <div id="image-preview" style="margin-top: 10px; display: none;">
                    <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                </div>
            </div>

            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" 
                          placeholder="Optional description text"></textarea>
            </div>

            <div id="link-fields" class="col-12" style="display: none;">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="link_url" class="form-label">Link URL *</label>
                        <input type="url" class="form-control" id="link_url" name="link_url" 
                               placeholder="https://example.com/page">
                    </div>
                    <div class="col-md-6">
                        <label for="link_text" class="form-label">Link Text</label>
                        <input type="text" class="form-control" id="link_text" name="link_text" 
                               placeholder="Click Here">
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label for="display_order" class="form-label">Display Order</label>
                <input type="number" class="form-control" id="display_order" name="display_order" 
                       value="0" min="0">
                <div class="form-text">Lower numbers appear first</div>
            </div>
            <div class="col-md-6">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                    <label class="form-check-label" for="is_active">
                        Active (visible on website)
                    </label>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Create Slider</button>
            </div>
        </form>
    </div>

    <div class="admin-form-section">
        <h3>All Sliders</h3>
        <?php if (empty($sliders)): ?>
            <div class="alert alert-info">No sliders found. Create your first slider above.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 80px;">Preview</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th>Created</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sliders as $slider): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($slider['image_url'])): ?>
                                        <img src="<?= htmlspecialchars(get_image_url($slider['image_url'])) ?>" 
                                             alt="<?= htmlspecialchars($slider['title']) ?>"
                                             class="img-thumbnail"
                                             style="width: 60px; height: 40px; object-fit: cover;"
                                             onerror="this.style.display='none';">
                                    <?php else: ?>
                                        <span class="text-muted small">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($slider['title']) ?></strong>
                                    <?php if (!empty($slider['description'])): ?>
                                        <br><small class="text-muted">
                                            <?= htmlspecialchars(mb_substr($slider['description'], 0, 50)) ?>
                                            <?= mb_strlen($slider['description']) > 50 ? '...' : '' ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $slider['slider_type'] === 'link' ? 'primary' : 'secondary' ?>">
                                        <?= ucfirst($slider['slider_type']) ?>
                                    </span>
                                    <?php if ($slider['slider_type'] === 'link' && !empty($slider['link_url'])): ?>
                                        <br><small class="text-muted">
                                            <a href="<?= htmlspecialchars($slider['link_url']) ?>" 
                                               target="_blank" class="text-decoration-none">
                                                <?= htmlspecialchars($slider['link_text'] ?? $slider['link_url']) ?>
                                            </a>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $slider['is_active'] ? 'success' : 'danger' ?>">
                                        <?= $slider['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td><?= $slider['display_order'] ?></td>
                                <td>
                                    <small class="text-muted"><?= date('M d, Y', strtotime($slider['created_at'])) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= admin_url('index.php?page=sliders&action=edit&id=' . $slider['id']) ?>" 
                                           class="btn btn-outline-secondary">Edit</a>
                                        <form method="POST" 
                                              action="<?= admin_url('index.php?page=sliders&action=toggleActive') ?>" 
                                              style="display: inline;">
                                            <input type="hidden" name="id" value="<?= $slider['id'] ?>">
                                            <button type="submit" 
                                                    class="btn btn-outline-<?= $slider['is_active'] ? 'warning' : 'success' ?>"
                                                    title="<?= $slider['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                                <?= $slider['is_active'] ? 'Deactivate' : 'Activate' ?>
                                            </button>
                                        </form>
                                        <form method="POST" 
                                              action="<?= admin_url('index.php?page=sliders&action=delete') ?>" 
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this slider? This action cannot be undone.');">
                                            <input type="hidden" name="id" value="<?= $slider['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger">
                                                Delete
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
                        Showing <?= count($sliders) ?> of <?= $pagination['total'] ?> sliders
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    // Toggle link fields based on slider type
    const sliderTypeSelect = document.getElementById('slider_type');
    const linkFields = document.getElementById('link-fields');
    const linkUrlInput = document.getElementById('link_url');

    function toggleLinkFields() {
        if (sliderTypeSelect.value === 'link') {
            linkFields.style.display = 'block';
            linkUrlInput.setAttribute('required', 'required');
        } else {
            linkFields.style.display = 'none';
            linkUrlInput.removeAttribute('required');
            linkUrlInput.value = '';
            document.getElementById('link_text').value = '';
        }
    }

    if (sliderTypeSelect) {
        sliderTypeSelect.addEventListener('change', toggleLinkFields);
        toggleLinkFields(); // Initialize on page load
    }

    // Image preview for file input
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');

    if (imageInput && imagePreview && previewImg) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB limit. Please choose a smaller file.');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Please choose an image file (JPEG, PNG, GIF, or WebP).');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });
    }
})();
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
