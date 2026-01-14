<?php
/**
 * Wishlist View
 */
require_once __DIR__ . '/../../app/helpers/helpers.php';

if (!function_exists('format_price')) {
    require_once __DIR__ . '/../../app/helpers/helpers.php';
}
?>
<section class="wishlist-main">
  <div class="container">
    <div class="wishlist-header">
      <h1>My Wishlist</h1>
      <p>Your saved products</p>
    </div>

    <?php if (!empty($products)): ?>
      <div class="wishlist-table-wrapper">
        <table class="wishlist-table">
          <thead>
            <tr>
              <th class="col-image">Image</th>
              <th class="col-product">Product</th>
              <th class="col-price">Price</th>
              <th class="col-rating">Rating</th>
              <th class="col-stock">Stock Status</th>
              <th class="col-actions">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product): ?>
              <?php 
              $productImage = null;
              if (!empty($product['main_image'])) {
                $productImage = get_image_url($product['main_image']);
              }
              $minPrice = $product['min_price'] ?? null;
              $minOfferPrice = $product['min_offer_price'] ?? null;
              $productTitle = $product['title'] ?? $product['name'] ?? '';
              $productSummary = $product['summary'] ?? '';
              
              $hasOffer = ($minOfferPrice !== null && $minPrice !== null && $minOfferPrice < $minPrice);
              $discountPercent = 0;
              if ($hasOffer && $minPrice > 0) {
                $discountPercent = round((($minPrice - $minOfferPrice) / $minPrice) * 100);
              }
              $isOutOfStock = isset($product['is_out_of_stock']) && $product['is_out_of_stock'];
              
              $rating = $product['rating'] ?? 0;
              $reviewCount = $product['review_count'] ?? 0;
              $roundedRating = round($rating);
              ?>
              <tr class="wishlist-item" data-product-id="<?= e($product['id']) ?>">
                <td class="col-image">
                  <a href="<?= url('product?slug=' . urlencode($product['slug'])) ?>" class="product-image-link">
                    <?php if ($productImage): ?>
                      <img src="<?= e($productImage) ?>" alt="<?= e($productTitle) ?>" class="product-thumbnail">
                    <?php else: ?>
                      <div class="product-thumbnail-placeholder">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                          <circle cx="8.5" cy="8.5" r="1.5"></circle>
                          <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                      </div>
                    <?php endif; ?>
                    <?php if ($hasOffer && $discountPercent > 0): ?>
                      <span class="table-offer-badge">-<?= $discountPercent ?>%</span>
                    <?php endif; ?>
                  </a>
                </td>
                <td class="col-product">
                  <a href="<?= url('product?slug=' . urlencode($product['slug'])) ?>" class="product-name-link">
                    <h3><?= e($productTitle) ?></h3>
                  </a>
                  <?php if ($productSummary): ?>
                    <p class="product-summary"><?= e($productSummary) ?></p>
                  <?php elseif (!empty($product['description'])): ?>
                    <p class="product-summary"><?= e(mb_substr(strip_tags($product['description']), 0, 100)) ?><?= mb_strlen(strip_tags($product['description'])) > 100 ? '...' : '' ?></p>
                  <?php endif; ?>
                </td>
                <td class="col-price">
                  <?php if ($minPrice !== null): ?>
                    <?php if ($hasOffer): ?>
                      <div class="price-container">
                        <span class="price original-price-strike"><?= format_price($minPrice) ?></span>
                        <span class="price offer-price"><?= format_price($minOfferPrice) ?></span>
                      </div>
                    <?php else: ?>
                      <span class="price"><?= format_price($minPrice) ?></span>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="price">Price on request</span>
                  <?php endif; ?>
                </td>
                <td class="col-rating">
                  <div class="product-rating-display">
                    <div class="rating-stars">
                      <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if ($i <= $roundedRating): ?>
                          <span class="star filled">★</span>
                        <?php else: ?>
                          <span class="star">☆</span>
                        <?php endif; ?>
                      <?php endfor; ?>
                    </div>
                    <?php if ($reviewCount > 0): ?>
                      <span class="review-count">(<?= $reviewCount ?>)</span>
                    <?php endif; ?>
                  </div>
                </td>
                <td class="col-stock">
                  <?php if ($isOutOfStock): ?>
                    <span class="stock-badge out-of-stock">Out of Stock</span>
                  <?php else: ?>
                    <span class="stock-badge in-stock">In Stock</span>
                  <?php endif; ?>
                </td>
                <td class="col-actions">
                  <div class="action-buttons">
                    <a href="<?= url('product?slug=' . urlencode($product['slug'])) ?>" class="btn-view" title="View Product">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                    </a>
                    <button type="button" class="btn-remove" data-product-id="<?= e($product['id']) ?>" title="Remove from Wishlist">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty-wishlist">
        <div class="empty-icon">❤️</div>
        <h2>Your wishlist is empty</h2>
        <p>Start adding products to your wishlist to save them for later.</p>
        <a href="<?= url('categories') ?>" class="btn-browse">Browse Products</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
// Wishlist remove functionality
(function() {
  // Show toast message notification
  function showToastMessage(message, type = 'success') {
    const existingToast = document.querySelector('.toast-message');
    if (existingToast) {
      existingToast.remove();
    }
    
    const toast = document.createElement('div');
    toast.className = 'toast-message' + (type === 'error' ? ' error' : '');
    toast.textContent = message;
    document.body.appendChild(toast);
    
    toast.classList.add('show');
    
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    }, 3000);
  }

  // Remove buttons
  document.querySelectorAll('.btn-remove').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const productId = this.dataset.productId;
      const row = this.closest('.wishlist-item');
      const btn = this;
      
      if (!productId || !row) return;
      
      // Disable button during request
      btn.disabled = true;
      btn.style.opacity = '0.6';
      
      fetch('/online-sp/wishlist?action=toggle', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ product_id: productId })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        if (data.login_required) {
          showToastMessage(data.message || 'Please login then only wishlist', 'error');
          btn.disabled = false;
          btn.style.opacity = '1';
        } else if (data.success && !data.in_wishlist) {
          // Remove from wishlist - fade out and remove row
          row.style.transition = 'opacity 0.3s, transform 0.3s';
          row.style.opacity = '0';
          row.style.transform = 'translateX(-20px)';
          
          setTimeout(() => {
            row.remove();
            
            // Check if table is now empty
            const tbody = document.querySelector('.wishlist-table tbody');
            const remainingRows = tbody.querySelectorAll('.wishlist-item');
            if (remainingRows.length === 0) {
              // Reload page to show empty state
              window.location.reload();
            }
          }, 300);
        } else {
          showToastMessage(data.message || 'Failed to remove from wishlist', 'error');
          btn.disabled = false;
          btn.style.opacity = '1';
        }
      })
      .catch(error => {
        console.error('Wishlist error:', error);
        showToastMessage('Failed to remove from wishlist. Please try again.', 'error');
        btn.disabled = false;
        btn.style.opacity = '1';
      });
    });
  });
})();
</script>
