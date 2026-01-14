<?php
/**
 * Product Edit View
 */
$title = 'Edit Product';
$product = $product ?? null;
$productId = $product['id'] ?? 0;
ob_start();
?>
<div class="admin-header">
    <h1>Edit Product</h1>
    <p>Update product information</p>
</div>

<?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="admin-panel">
    <form method="POST" action="" id="product-edit-form" enctype="multipart/form-data" class="row g-3">
        <!-- Basic Information -->
        <div class="col-12">
            <h4 class="border-bottom pb-2 mb-3">Basic Information</h4>
        </div>

        <div class="col-md-6">
            <label for="category_id" class="form-label">Category *</label>
            <select class="form-select" id="category_id" name="category_id" required>
                <option value="">Select Category</option>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= ($product['category_id'] ?? null) == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label for="subcategory_id" class="form-label">Subcategory</label>
            <select class="form-select" id="subcategory_id" name="subcategory_id">
                <option value="">Select Subcategory (Optional)</option>
            </select>
            <div class="form-text">Optional: Select a subcategory</div>
        </div>

        <div class="col-md-6">
            <label for="product_code" class="form-label">Product Code</label>
            <input type="text" class="form-control" id="product_code" name="product_code" 
                   value="<?= htmlspecialchars($product['product_code'] ?? '') ?>"
                   placeholder="PROD-001">
            <div class="form-text">Unique product code identifier</div>
        </div>

        <div class="col-md-6">
            <label for="title" class="form-label">Product Title *</label>
            <input type="text" class="form-control" id="title" name="title" 
                   value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
        </div>

        <div class="col-md-6">
            <label for="slug" class="form-label">Slug *</label>
            <input type="text" class="form-control" id="slug" name="slug" 
                   value="<?= htmlspecialchars($product['slug'] ?? '') ?>" required>
            <div class="form-text">URL-friendly identifier (auto-generated from title)</div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-6">
                    <label class="form-label">Status</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" 
                               <?= ($product['is_active'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label">Stock</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_out_of_stock" value="1" id="is_out_of_stock"
                               <?= ($product['is_out_of_stock'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_out_of_stock">Out of Stock</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>

        <!-- Main Image Upload -->
        <div class="col-12 mt-4">
            <h4 class="border-bottom pb-2 mb-3">Main Image</h4>
        </div>

        <div class="col-12">
            <?php if (!empty($product['main_image'])): ?>
                <?php $existingMainImageUrl = get_image_url($product['main_image']); ?>
                <div class="mb-3">
                    <label class="form-label">Current Main Image</label>
                    <div>
                        <img src="<?= htmlspecialchars($existingMainImageUrl) ?>" 
                             alt="Current Main Image" 
                             style="max-width: 200px; border-radius: 8px; border: 2px solid rgba(50, 198, 141, 0.2);">
                    </div>
                </div>
            <?php endif; ?>
            
            <label for="main_image" class="form-label"><?= !empty($product['main_image']) ? 'Update Main Product Image' : 'Main Product Image *' ?></label>
            <input type="file" class="form-control" id="main_image" name="main_image" accept="image/*" <?= empty($product['main_image']) ? 'required' : '' ?>>
            <div class="form-text">Leave empty to keep current image. Main product image (JPG, PNG, GIF, WEBP - Max 5MB)</div>
            <div id="main_image_preview" class="mt-2" style="display: none;">
                <img src="" alt="Preview" style="max-width: 200px; border-radius: 8px; border: 2px solid rgba(50, 198, 141, 0.2);">
            </div>
        </div>

        <!-- Additional Images -->
        <div class="col-12 mt-4">
            <h4 class="border-bottom pb-2 mb-3">Additional Product Images</h4>
        </div>

        <?php if (!empty($product['images']) && is_array($product['images'])): ?>
            <div class="col-12 mb-3">
                <label class="form-label">Existing Additional Images</label>
                <div class="row g-2">
                    <?php foreach ($product['images'] as $img): ?>
                        <?php if (isset($img['image_path']) && isset($img['id'])): ?>
                            <?php $imgUrl = get_image_url($img['image_path']); ?>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body p-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="keep_existing_images[]" 
                                                   value="<?= (int)$img['id'] ?>" 
                                                   id="keep_image_<?= (int)$img['id'] ?>" 
                                                   checked>
                                            <label class="form-check-label" for="keep_image_<?= (int)$img['id'] ?>">
                                                <img src="<?= htmlspecialchars($imgUrl) ?>" 
                                                     alt="Product Image" 
                                                     style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px;">
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Uncheck to remove</small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-12">
            <label for="additional_images" class="form-label">Add New Additional Images</label>
            <input type="file" class="form-control" id="additional_images" name="additional_images[]" 
                   accept="image/*" multiple>
            <div class="form-text">You can select multiple images (JPG, PNG, GIF, WEBP - Max 5MB each)</div>
            <div id="additional_images_preview" class="mt-3 d-flex flex-wrap gap-2"></div>
        </div>

        <!-- Product Names in Different Languages -->
        <div class="col-12 mt-4">
            <h4 class="border-bottom pb-2 mb-3">Product Names in Different Languages</h4>
            <div class="form-text mb-3">Add product name translations for different languages</div>
        </div>

        <div class="col-12">
            <div class="table-responsive">
                <table class="table" id="language-table">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 40%;">Language Code</th>
                            <th style="width: 50%;">Product Name</th>
                            <th style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="language-tbody">
                        <?php if (!empty($product['languages']) && is_array($product['languages'])): ?>
                            <?php foreach ($product['languages'] as $lang): ?>
                                <?php if (isset($lang['language_code']) || isset($lang['product_name'])): ?>
                                    <tr class="language-row">
                                        <td>
                                            <input type="text" class="form-control form-control-sm" 
                                                   name="language_code[]" 
                                                   value="<?= htmlspecialchars($lang['language_code'] ?? '') ?>"
                                                   placeholder="e.g., en, hi, ml, ta">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" 
                                                   name="language_name[]" 
                                                   value="<?= htmlspecialchars($lang['product_name'] ?? '') ?>"
                                                   placeholder="Product name in this language">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-language-row">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (empty($product['languages']) || !is_array($product['languages']) || count($product['languages']) == 0): ?>
                            <tr class="language-row">
                                <td>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="language_code[]" placeholder="e.g., en, hi, ml, ta">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="language_name[]" placeholder="Product name in this language">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-language-row" style="display: none;">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-language-row">
                + Add Language
            </button>
        </div>

        <!-- Submit Buttons -->
        <div class="col-12 mt-4 border-top pt-3">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-save"></i> Update Product
            </button>
            <a href="<?= admin_url('index.php?page=products') ?>" class="btn btn-secondary btn-lg ms-2">
                Cancel
            </a>
            <a href="<?= admin_url('index.php?page=products&action=detail&id=' . $productId) ?>" class="btn btn-info btn-lg ms-2">
                View Details
            </a>
        </div>
    </form>
</div>

<script>
(function() {
    // Auto-generate slug from title (only if slug is empty)
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    
    if (titleInput && slugInput) {
        const originalSlug = slugInput.value;
        if (!originalSlug) {
            titleInput.addEventListener('input', function() {
                if (!slugInput.dataset.edited) {
                    const slug = this.value
                        .toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    slugInput.value = slug;
                }
            });
        }

        slugInput.addEventListener('input', function() {
            this.dataset.edited = 'true';
        });
    }

    // Main image preview
    const mainImageInput = document.getElementById('main_image');
    const mainImagePreview = document.getElementById('main_image_preview');
    
    if (mainImageInput && mainImagePreview) {
        mainImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    mainImagePreview.querySelector('img').src = e.target.result;
                    mainImagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                mainImagePreview.style.display = 'none';
            }
        });
    }

    // Additional images preview
    const additionalImagesInput = document.getElementById('additional_images');
    const additionalImagesPreview = document.getElementById('additional_images_preview');
    
    if (additionalImagesInput && additionalImagesPreview) {
        additionalImagesInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'position-relative';
                    div.style.width = '150px';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview ${index + 1}" 
                             style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid rgba(50, 198, 141, 0.2);">
                        <span style="position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.7); color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px;">${index + 1}</span>
                    `;
                    additionalImagesPreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // Add language row
    const addLanguageRowBtn = document.getElementById('add-language-row');
    const languageTbody = document.getElementById('language-tbody');
    
    if (addLanguageRowBtn && languageTbody) {
        addLanguageRowBtn.addEventListener('click', function() {
            const newRow = document.createElement('tr');
            newRow.className = 'language-row';
            newRow.innerHTML = `
                <td>
                    <input type="text" class="form-control form-control-sm" 
                           name="language_code[]" placeholder="e.g., en, hi, ml, ta">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" 
                           name="language_name[]" placeholder="Product name in this language">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-language-row">
                        Remove
                    </button>
                </td>
            `;
            languageTbody.appendChild(newRow);
            updateRemoveButtons();
        });
    }

    // Remove language row
    function updateRemoveButtons() {
        const rows = languageTbody.querySelectorAll('.language-row');
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-language-row');
            if (rows.length > 1) {
                removeBtn.style.display = 'inline-block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    languageTbody.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-language-row')) {
            e.target.closest('.language-row').remove();
            updateRemoveButtons();
        }
    });

    // Initialize
    updateRemoveButtons();
    
    // Category/Subcategory dynamic loading
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');
    const allSubcategories = <?= json_encode($subcategories ?? []) ?>;
    const currentCategoryId = <?= $product['category_id'] ?? 'null' ?>;
    const currentSubcategoryId = <?= $product['group_id'] ?? 'null' ?>;
    
    if (categorySelect && subcategorySelect) {
        function updateSubcategories() {
            const selectedCategoryId = categorySelect.value;
            subcategorySelect.innerHTML = '<option value="">Select Subcategory (Optional)</option>';
            
            if (selectedCategoryId) {
                const filteredSubcategories = allSubcategories.filter(sub => sub.category_id == selectedCategoryId);
                filteredSubcategories.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = sub.name;
                    if (currentSubcategoryId && sub.id == currentSubcategoryId) {
                        option.selected = true;
                    }
                    subcategorySelect.appendChild(option);
                });
            }
        }
        
        // Load subcategories on page load if category is selected
        if (currentCategoryId) {
            updateSubcategories();
        }
        
        // Load subcategories when category changes
        categorySelect.addEventListener('change', updateSubcategories);
    }
})();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

