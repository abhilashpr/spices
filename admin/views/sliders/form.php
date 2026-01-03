<?php
/**
 * Slider Form View
 * Used for both creating and editing sliders
 */
$title = ($slider ? 'Edit' : 'Add') . ' Slider';
ob_start();
?>
<div class="admin-header">
    <h1><?= $slider ? 'Edit Slider' : 'Add New Slider' ?></h1>
    <p><?= $slider ? 'Update slider information' : 'Create a new slider for your website' ?></p>
</div>

<div class="admin-panel">
    <form method="POST" action="" id="slider-form" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?= $slider ? 'update' : 'create' ?>">
        
        <div class="admin-form-row">
            <div class="admin-form-group">
                <label for="title">Slider Title *</label>
                <input type="text" id="title" name="title" 
                       value="<?= htmlspecialchars($slider['title'] ?? '') ?>" 
                       required 
                       placeholder="Enter slider title">
            </div>
            <div class="admin-form-group">
                <label for="slider_type">Slider Type *</label>
                <select id="slider_type" name="slider_type" required>
                    <option value="static" <?= (!isset($slider) || $slider['slider_type'] === 'static') ? 'selected' : '' ?>>
                        Static Slider
                    </option>
                    <option value="link" <?= (isset($slider) && $slider['slider_type'] === 'link') ? 'selected' : '' ?>>
                        Slider with Link
                    </option>
                </select>
                <small class="admin-form-help">
                    Static: Image only | Link: Clickable slider with URL
                </small>
            </div>
        </div>

        <div class="admin-form-group">
            <label for="image">Slider Image <?= $slider ? '' : '*' ?></label>
            <input type="file" id="image" name="image" 
                   accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                   <?= !$slider ? 'required' : '' ?>>
            <small class="admin-form-help">
                <?php if ($slider): ?>
                    Upload a new image to replace the current one, or leave empty to keep the current image.
                <?php else: ?>
                    Upload an image file (JPEG, PNG, GIF, or WebP). Maximum size: 5MB
                <?php endif; ?>
            </small>
            <?php if (!empty($slider['image_url'])): ?>
                <div style="margin-top: 10px;">
                    <p style="margin-bottom: 5px; font-size: 13px; color: #666;">Current Image:</p>
                    <img src="<?= htmlspecialchars(get_image_url($slider['image_url'])) ?>" 
                         alt="Current Image" 
                         id="current-image"
                         style="max-width: 300px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px; padding: 4px; display: block;"
                         onerror="this.style.display='none';">
                </div>
            <?php endif; ?>
            <div id="image-preview" style="margin-top: 10px; display: none;">
                <p style="margin-bottom: 5px; font-size: 13px; color: #666;">New Image Preview:</p>
                <img id="preview-img" src="" alt="Preview" style="max-width: 300px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px; padding: 4px; display: block;">
            </div>
        </div>

        <div class="admin-form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" 
                      placeholder="Optional description text"><?= htmlspecialchars($slider['description'] ?? '') ?></textarea>
        </div>

        <div id="link-fields" style="display: <?= (isset($slider) && $slider['slider_type'] === 'link') ? 'block' : 'none' ?>;">
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label for="link_url">Link URL <?= (isset($slider) && $slider['slider_type'] === 'link') ? '*' : '' ?></label>
                    <input type="url" id="link_url" name="link_url" 
                           value="<?= htmlspecialchars($slider['link_url'] ?? '') ?>" 
                           placeholder="https://example.com/page"
                           <?= (isset($slider) && $slider['slider_type'] === 'link') ? 'required' : '' ?>>
                    <small class="admin-form-help">Where users will be directed when clicking the slider</small>
                </div>
                <div class="admin-form-group">
                    <label for="link_text">Link Text</label>
                    <input type="text" id="link_text" name="link_text" 
                           value="<?= htmlspecialchars($slider['link_text'] ?? '') ?>" 
                           placeholder="Click Here">
                    <small class="admin-form-help">Optional text for the link button</small>
                </div>
            </div>
        </div>

        <div class="admin-form-row">
            <div class="admin-form-group">
                <label for="display_order">Display Order</label>
                <input type="number" id="display_order" name="display_order" 
                       value="<?= $slider['display_order'] ?? 0 ?>" 
                       min="0">
                <small class="admin-form-help">Lower numbers appear first in the slider</small>
            </div>
            <div class="admin-form-group">
                <label style="display: flex; align-items: center; margin-top: 24px;">
                    <input type="checkbox" name="is_active" value="1" 
                           <?= (!isset($slider) || $slider['is_active']) ? 'checked' : '' ?> 
                           style="margin-right: 8px;">
                    <span>Active (visible on website)</span>
                </label>
            </div>
        </div>

        <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e0e0e0;">
            <button type="submit" class="admin-btn admin-btn-primary">
                <?= $slider ? 'Update Slider' : 'Create Slider' ?>
            </button>
            <a href="<?= admin_url('index.php?page=sliders') ?>" 
               class="admin-btn admin-btn-secondary" 
               style="text-decoration: none; display: inline-block; margin-left: 12px;">
                Cancel
            </a>
        </div>
    </form>
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
        }
    }

    if (sliderTypeSelect) {
        sliderTypeSelect.addEventListener('change', toggleLinkFields);
    }

    // Image preview for file input
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const currentImage = document.getElementById('current-image');

    if (imageInput && imagePreview && previewImg) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB limit. Please choose a smaller file.');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    if (currentImage) currentImage.style.display = 'block';
                    return;
                }

                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Please choose an image file (JPEG, PNG, GIF, or WebP).');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    if (currentImage) currentImage.style.display = 'block';
                    return;
                }

                // Hide current image and show preview
                if (currentImage) currentImage.style.display = 'none';
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
                if (currentImage) currentImage.style.display = 'block';
            }
        });
    }
})();
</script>

<style>
.admin-form-help {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    color: #6c757d;
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

