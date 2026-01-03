<?php
/**
 * Product Detail View
 */
$product = $product ?? [];
$benefitsByType = $benefitsByType ?? [
    'health_benefits' => [],
    'how_to_use' => [],
    'how_to_store' => []
];
$additionalData = $additionalData ?? [];
$skus = $skus ?? [];

$title = 'Product Detail - ' . htmlspecialchars($product['name'] ?? 'Product');
ob_start();
?>
<div class="admin-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Product Detail</h1>
            <p>Manage all aspects of this product</p>
        </div>
        <div>
            <a href="<?= admin_url('index.php?page=products') ?>" class="btn btn-secondary">
                <span>←</span> Back to Products
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
    <!-- Product Overview Section -->
    <div class="product-overview-section mb-4">
        <div class="row">
            <div class="col-md-3">
                <?php if (!empty($product['main_image'] ?? '')): ?>
                    <?php $imageUrl = get_image_url($product['main_image']); ?>
                    <img src="<?= htmlspecialchars($imageUrl) ?>" 
                         alt="<?= htmlspecialchars($product['name'] ?? 'Product') ?>" 
                         class="product-main-image">
                <?php else: ?>
                    <div class="product-image-placeholder">
                        <span>No Image</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-9">
                <h2 class="product-title"><?= htmlspecialchars($product['name'] ?? 'Product') ?></h2>
                <div class="product-meta mb-3">
                    <span class="badge bg-<?= ($product['is_active'] ?? 0) ? 'success' : 'secondary' ?> me-2">
                        <?= ($product['is_active'] ?? 0) ? 'Active' : 'Inactive' ?>
                    </span>
                    <span class="badge bg-<?= ($product['is_out_of_stock'] ?? 0) ? 'danger' : 'success' ?> me-2">
                        <?= ($product['is_out_of_stock'] ?? 0) ? 'Out of Stock' : 'In Stock' ?>
                    </span>
                    <?php if (!empty($product['product_code'] ?? '')): ?>
                        <span class="badge bg-info me-2">Code: <?= htmlspecialchars($product['product_code']) ?></span>
                    <?php endif; ?>
                    <span class="badge bg-info">Slug: <?= htmlspecialchars($product['slug'] ?? '') ?></span>
                </div>
                <div class="product-actions">
                    <a href="<?= admin_url('index.php?page=products&action=edit&id=' . ($product['id'] ?? 0)) ?>" class="btn btn-primary me-2 mb-2">
                        ✏️ Edit Product
                    </a>
                    <a href="<?= admin_url('index.php?page=product_skus&action=manage&product_id=' . ($product['id'] ?? 0)) ?>" class="btn btn-outline-primary me-2 mb-2">
                        📦 Manage SKUs
                    </a>
                    <form method="POST" action="<?= admin_url('index.php?page=products&action=delete') ?>" 
                          style="display: inline;"
                          onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                        <input type="hidden" name="id" value="<?= $product['id'] ?? 0 ?>">
                        <button type="submit" class="btn btn-outline-danger mb-2">
                            🗑️ Delete Product
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="row g-4">
        <!-- Left Sidebar - Product Information -->
        <div class="col-lg-4 col-md-12">
            <div class="product-sidebar">
                <!-- Basic Information -->
                <div class="info-card mb-4">
                    <h3 class="info-card-title">Basic Information</h3>
                    <div class="info-card-content">
                        <div class="info-item">
                            <label>Product Code:</label>
                            <span><?= htmlspecialchars($product['product_code'] ?? '-') ?></span>
                        </div>
                        <div class="info-item">
                            <label>Title:</label>
                            <span><?= htmlspecialchars($product['name'] ?? '') ?></span>
                        </div>
                        <div class="info-item">
                            <label>Slug:</label>
                            <span><?= htmlspecialchars($product['slug'] ?? '') ?></span>
                        </div>
                        <?php if (!empty($product['description'] ?? '')): ?>
                            <div class="info-item">
                                <label>Description:</label>
                                <span><?= nl2br(htmlspecialchars($product['description'])) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Languages -->
                <div class="info-card mb-4">
                    <h3 class="info-card-title">Product Names in Different Languages</h3>
                    <div class="info-card-content">
                        <?php if (!empty($product['languages']) && is_array($product['languages'])): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($product['languages'] as $lang): ?>
                                            <tr>
                                                <td><code><?= htmlspecialchars(strtoupper($lang['language_code'])) ?></code></td>
                                                <td><?= htmlspecialchars($lang['product_name']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No language translations added.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Images -->
                <div class="info-card mb-4">
                    <h3 class="info-card-title">Product Images</h3>
                    <div class="info-card-content">
                        <?php if (!empty($product['images']) && is_array($product['images'])): ?>
                            <div class="product-images-grid">
                                <?php foreach ($product['images'] as $img): ?>
                                    <?php $imgUrl = get_image_url($img['image_path']); ?>
                                    <div class="product-image-item">
                                        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Product Image">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No additional images added.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product SKUs -->
                <div class="info-card">
                    <h3 class="info-card-title">Product SKUs (<?= count($skus) ?>)</h3>
                    <div class="info-card-content">
                        <?php if (!empty($skus)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Value</th>
                                            <th>Unit</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($skus, 0, 5) as $sku): ?>
                                            <tr>
                                                <td><strong><?= number_format($sku['value'], 2) ?></strong></td>
                                                <td><?= htmlspecialchars($sku['unit_symbol'] ?? $sku['unit_name']) ?></td>
                                                <td>$<?= number_format($sku['price'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if (count($skus) > 5): ?>
                                <div class="mt-3">
                                    <a href="<?= admin_url('index.php?page=product_skus&action=manage&product_id=' . ($product['id'] ?? 0)) ?>" class="btn btn-sm btn-outline-primary">
                                        View All (<?= count($skus) ?>) →
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="mt-3">
                                    <a href="<?= admin_url('index.php?page=product_skus&action=manage&product_id=' . ($product['id'] ?? 0)) ?>" class="btn btn-sm btn-outline-primary">
                                        Manage SKUs →
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted mb-3">No SKUs added yet.</p>
                            <a href="<?= admin_url('index.php?page=product_skus&action=manage&product_id=' . ($product['id'] ?? 0)) ?>" class="btn btn-sm btn-primary">
                                Add First SKU →
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content Area -->
        <div class="col-lg-8 col-md-12">
            <!-- Additional Data -->
            <div class="info-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="info-card-title mb-0">Additional Data <?= !empty($additionalData) ? '(' . count($additionalData) . ')' : '' ?></h3>
                    <a href="<?= admin_url('index.php?page=product_additional_data&action=manage&product_id=' . ($product['id'] ?? 0)) ?>" 
                       class="btn btn-outline-primary btn-sm">
                        ➕ Add Additional Data
                    </a>
                </div>
                <div class="info-card-content">
                    <?php if (!empty($additionalData)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Key</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($additionalData as $data): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($data['data_key']) ?></strong></td>
                                            <td><?= htmlspecialchars($data['data_value']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No additional data added yet. Click "Add Additional Data" to add key-value pairs.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Benefits -->
            <div class="info-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="info-card-title mb-0">Product Benefits</h3>
                    <a href="<?= admin_url('index.php?page=product_benefits&action=manage&product_id=' . ($product['id'] ?? 0)) ?>" 
                       class="btn btn-outline-primary btn-sm">
                        ➕ Add Product Benefits
                    </a>
                </div>
                <div class="info-card-content">
                    <div class="row g-3">
                        <!-- Health Benefits -->
                        <div class="col-md-4">
                            <div class="benefit-section">
                                <h4 class="benefit-section-title">Health Benefits</h4>
                                <?php if (!empty($benefitsByType['health_benefits'])): ?>
                                    <ul class="benefit-list">
                                        <?php foreach ($benefitsByType['health_benefits'] as $benefit): ?>
                                            <li class="benefit-item"><?= htmlspecialchars($benefit['benefit_text'] ?? '') ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted small mb-0">No health benefits added yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- How to Use -->
                        <div class="col-md-4">
                            <div class="benefit-section">
                                <h4 class="benefit-section-title">How to Use</h4>
                                <?php if (!empty($benefitsByType['how_to_use'])): ?>
                                    <ul class="benefit-list">
                                        <?php foreach ($benefitsByType['how_to_use'] as $benefit): ?>
                                            <li class="benefit-item"><?= htmlspecialchars($benefit['benefit_text'] ?? '') ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted small mb-0">No usage instructions added yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- How to Store -->
                        <div class="col-md-4">
                            <div class="benefit-section">
                                <h4 class="benefit-section-title">How to Store</h4>
                                <?php if (!empty($benefitsByType['how_to_store'])): ?>
                                    <ul class="benefit-list">
                                        <?php foreach ($benefitsByType['how_to_store'] as $benefit): ?>
                                            <li class="benefit-item"><?= htmlspecialchars($benefit['benefit_text'] ?? '') ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted small mb-0">No storage instructions added yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.product-overview-section {
    padding: 24px;
    background: linear-gradient(135deg, rgba(50, 198, 141, 0.05), rgba(50, 198, 141, 0.02));
    border-radius: 16px;
    border: 1px solid rgba(50, 198, 141, 0.15);
    margin-bottom: 24px;
}

.product-main-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 12px;
    border: 2px solid rgba(50, 198, 141, 0.2);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.product-image-placeholder {
    width: 100%;
    height: 250px;
    background: rgba(50, 198, 141, 0.1);
    border: 2px dashed rgba(50, 198, 141, 0.3);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(50, 198, 141, 0.6);
    font-weight: 600;
}

.product-title {
    font-family: "Playfair Display", serif;
    font-size: 2rem;
    margin-bottom: 12px;
    color: #09342a;
}

.product-meta {
    margin-bottom: 16px;
}

.product-actions {
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.product-sidebar {
    position: sticky;
    top: 20px;
}

.info-card {
    background: white;
    border: 1px solid rgba(50, 198, 141, 0.15);
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: box-shadow 0.3s ease;
}

.info-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.info-card-title {
    font-family: "Playfair Display", serif;
    font-size: 1.25rem;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid rgba(50, 198, 141, 0.15);
    color: #09342a;
}

.info-card-content {
    min-height: 60px;
}

.info-item {
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(50, 198, 141, 0.1);
}

.info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.info-item label {
    display: block;
    font-weight: 600;
    color: #6c757d;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 4px;
}

.info-item span {
    display: block;
    color: #212529;
    font-size: 0.95rem;
}

.product-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 10px;
}

.product-image-item {
    width: 100%;
    aspect-ratio: 1;
    overflow: hidden;
    border-radius: 8px;
    border: 2px solid rgba(50, 198, 141, 0.2);
}

.product-image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-image-item:hover img {
    transform: scale(1.1);
}

.benefit-section {
    background: rgba(50, 198, 141, 0.03);
    border: 1px solid rgba(50, 198, 141, 0.1);
    border-radius: 10px;
    padding: 16px;
    height: 100%;
    min-height: 200px;
}

.benefit-section-title {
    font-family: "Playfair Display", serif;
    font-size: 1.1rem;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 2px solid rgba(50, 198, 141, 0.2);
    color: #09342a;
}

.benefit-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.benefit-item {
    padding: 8px 12px;
    margin-bottom: 6px;
    background: rgba(50, 198, 141, 0.05);
    border-left: 3px solid rgba(50, 198, 141, 0.3);
    border-radius: 6px;
    font-size: 0.9rem;
    line-height: 1.5;
    transition: all 0.2s ease;
}

.benefit-item:hover {
    background: rgba(50, 198, 141, 0.1);
    border-left-color: rgba(50, 198, 141, 0.6);
}

@media (min-width: 992px) {
    .col-lg-4 {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }
    .col-lg-8 {
        flex: 0 0 66.666667%;
        max-width: 66.666667%;
    }
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

