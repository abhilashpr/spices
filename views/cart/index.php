<?php
/**
 * Cart View
 */
require_once __DIR__ . '/../../app/helpers/helpers.php';

if (!function_exists('format_price')) {
    require_once __DIR__ . '/../../app/helpers/helpers.php';
}

// Debug: Log cart data
error_log("Cart view - is_logged_in: " . (is_logged_in() ? 'yes' : 'no'));
if (is_logged_in()) {
    $user = get_logged_in_user();
    error_log("Cart view - user ID: " . ($user['id'] ?? 'null'));
}
error_log("Cart view - cart count: " . count($cart ?? []));
if (!empty($cart)) {
    error_log("Cart view - first item: " . print_r($cart[0] ?? null, true));
}
?>
<section class="wishlist-main">
  <div class="container">
    <div class="wishlist-header">
      <h1>My Cart</h1>
      <p>Your shopping cart items</p>
    </div>

    <?php if (!empty($cart)): ?>
      <div class="wishlist-table-wrapper">
        <table class="wishlist-table">
          <thead>
            <tr>
              <th class="col-image">Image</th>
              <th class="col-product">Product</th>
              <th class="col-price">Price</th>
              <th class="col-quantity">Quantity</th>
              <th class="col-total">Total</th>
              <th class="col-actions">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart as $item): ?>
              <?php 
              $productImage = null;
              if (!empty($item['main_image'])) {
                $productImage = get_image_url($item['main_image']);
              }
              $price = $item['price'] ?? 0;
              $offerPrice = $item['offer_price'] ?? null;
              $quantity = $item['quantity'] ?? 1;
              $hasOffer = ($offerPrice !== null && $price > 0 && $offerPrice < $price);
              $displayPrice = $hasOffer ? $offerPrice : $price;
              $totalPrice = $displayPrice * $quantity;
              $productName = $item['product_name'] ?? 'Product';
              $unitSymbol = $item['unit_symbol'] ?? '';
              $skuValue = $item['sku_value'] ?? '';
              ?>
              <tr class="cart-item" data-cart-item-id="<?= e($item['cart_item_id'] ?? '') ?>">
                <td class="col-image">
                  <a href="<?= url('product?slug=' . urlencode($item['product_slug'] ?? '')) ?>" class="product-image-link">
                    <?php if ($productImage): ?>
                      <img src="<?= e($productImage) ?>" alt="<?= e($productName) ?>" class="product-thumbnail">
                    <?php else: ?>
                      <div class="product-thumbnail-placeholder">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                          <circle cx="8.5" cy="8.5" r="1.5"></circle>
                          <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                      </div>
                    <?php endif; ?>
                    <?php if ($hasOffer): ?>
                      <?php 
                      $discountPercent = round((($price - $offerPrice) / $price) * 100);
                      ?>
                      <span class="table-offer-badge">-<?= $discountPercent ?>%</span>
                    <?php endif; ?>
                  </a>
                </td>
                <td class="col-product">
                  <a href="<?= url('product?slug=' . urlencode($item['product_slug'] ?? '')) ?>" class="product-name-link">
                    <h3><?= e($productName) ?></h3>
                  </a>
                  <?php if ($skuValue || $unitSymbol): ?>
                    <p class="product-summary"><?= e($skuValue) ?><?= $unitSymbol ? ' ' . e($unitSymbol) : '' ?></p>
                  <?php endif; ?>
                </td>
                <td class="col-price">
                  <?php if ($hasOffer): ?>
                    <div class="price-container">
                      <span class="price original-price-strike"><?= format_price($price) ?></span>
                      <span class="price offer-price"><?= format_price($offerPrice) ?></span>
                    </div>
                  <?php else: ?>
                    <span class="price"><?= format_price($price) ?></span>
                  <?php endif; ?>
                </td>
                <td class="col-quantity">
                  <span class="quantity-display"><?= $quantity ?></span>
                </td>
                <td class="col-total">
                  <span class="price total-price"><?= format_price($totalPrice) ?></span>
                </td>
                <td class="col-actions">
                  <div class="action-buttons">
                    <a href="<?= url('product?slug=' . urlencode($item['product_slug'] ?? '')) ?>" class="btn-view" title="View Product">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                    </a>
                    <button type="button" class="btn-remove" data-cart-item-id="<?= e($item['cart_item_id'] ?? '') ?>" title="Remove from Cart">
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
      
      <div class="cart-actions">
        <a href="<?= url('checkout') ?>" class="btn-checkout">
          <span>Continue to Checkout</span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
      </div>
    <?php else: ?>
      <div class="empty-wishlist">
        <div class="empty-icon">🛒</div>
        <h2>Your cart is empty</h2>
        <p>Start adding products to your cart to get started.</p>
        <a href="<?= url('categories') ?>" class="btn-browse">Browse Products</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
// Cart remove functionality
(function() {
  // Show message notification at bottom right corner
  function showCartMessage(message, type = 'success') {
    const existingMessage = document.querySelector('.cart-message');
    if (existingMessage) {
      existingMessage.remove();
    }
    
    const messageEl = document.createElement('div');
    messageEl.className = 'cart-message' + (type === 'error' ? ' error' : ' success');
    messageEl.textContent = message;
    document.body.appendChild(messageEl);
    
    void messageEl.offsetHeight; // Force reflow
    setTimeout(() => {
      messageEl.classList.add('show');
    }, 10);
    
    setTimeout(() => {
      messageEl.classList.remove('show');
      setTimeout(() => {
        if (messageEl.parentNode) {
          messageEl.parentNode.removeChild(messageEl);
        }
      }, 400);
    }, 3500);
  }

  // Remove buttons
  document.querySelectorAll('.btn-remove').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const cartItemId = this.dataset.cartItemId;
      const row = this.closest('.cart-item');
      const btn = this;
      
      if (!cartItemId || !row) return;
      
      // Disable button during request
      btn.disabled = true;
      btn.style.opacity = '0.6';
      
      fetch('/online-sp/cart?action=remove', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ cart_item_id: cartItemId })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          // Show success message
          showCartMessage(data.message || 'Item removed from cart successfully!', 'success');
          
          // Remove row with animation
          row.style.transition = 'opacity 0.3s, transform 0.3s';
          row.style.opacity = '0';
          row.style.transform = 'translateX(-20px)';
          
          setTimeout(() => {
            row.remove();
            
            // Check if table is now empty
            const tbody = document.querySelector('.wishlist-table tbody');
            const remainingRows = tbody.querySelectorAll('.cart-item');
            if (remainingRows.length === 0) {
              // Reload page to show empty state
              window.location.reload();
            }
          }, 300);
        } else {
          showCartMessage(data.message || 'Failed to remove item from cart', 'error');
          btn.disabled = false;
          btn.style.opacity = '1';
        }
      })
      .catch(error => {
        console.error('Cart remove error:', error);
        showCartMessage('Failed to remove item from cart. Please try again.', 'error');
        btn.disabled = false;
        btn.style.opacity = '1';
      });
    });
  });
})();
</script>
