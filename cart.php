<?php
$pageTitle = 'Your Cart | Saffron & Spice';
include __DIR__ . '/includes/header.php';
?>
    <main class="cart-page-main">
      <section class="cart-main">
        <article class="glass-panel cart-items">
          <div class="cart-items-header">
            <h2>Shopping Cart</h2>
            <span class="cart-count">2 items</span>
          </div>
          <ul class="cart-list">
            <li class="cart-line">
              <figure class="cart-item-image">
                <div class="card-image aurora"></div>
              </figure>
              <div class="cart-item-details">
                <div class="cart-item-info">
                  <h3 class="cart-item-name">Amber Garam</h3>
                  <span class="cart-item-weight">500g</span>
                  <span class="cart-item-price-unit">$34 per unit</span>
                </div>
                <div class="cart-item-controls">
                  <div class="cart-quantity-control">
                    <button class="quantity-btn-decrease" type="button" aria-label="Decrease quantity">−</button>
                    <span class="quantity-value">2</span>
                    <button class="quantity-btn-increase" type="button" aria-label="Increase quantity">+</button>
                  </div>
                  <button class="remove-item-btn" type="button" aria-label="Remove item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                  </button>
                </div>
              </div>
              <div class="cart-item-total">
                <span class="cart-price">$68</span>
              </div>
            </li>
            <li class="cart-line">
              <figure class="cart-item-image">
                <div class="card-image ember-rise"></div>
              </figure>
              <div class="cart-item-details">
                <div class="cart-item-info">
                  <h3 class="cart-item-name">Charred Citrus Harissa</h3>
                  <span class="cart-item-weight">500g</span>
                  <span class="cart-item-price-unit">$29 per unit</span>
                </div>
                <div class="cart-item-controls">
                  <div class="cart-quantity-control">
                    <button class="quantity-btn-decrease" type="button" aria-label="Decrease quantity">−</button>
                    <span class="quantity-value">1</span>
                    <button class="quantity-btn-increase" type="button" aria-label="Increase quantity">+</button>
                  </div>
                  <button class="remove-item-btn" type="button" aria-label="Remove item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                  </button>
                </div>
              </div>
              <div class="cart-item-total">
                <span class="cart-price">$29</span>
              </div>
            </li>
          </ul>
          <div class="cart-actions">
            <a class="button outline" href="/online-sp/categories.php">
              <span>←</span> Continue Shopping
            </a>
          </div>
        </article>

        <aside class="glass-panel cart-summary">
          <div class="cart-summary-header">
            <h2>Order Summary</h2>
          </div>
          <div class="summary-details">
            <div class="summary-line">
              <span class="summary-label">Subtotal</span>
              <span class="summary-value">$97</span>
            </div>
            <div class="summary-line">
              <span class="summary-label">Shipping</span>
              <span class="summary-value free">Complimentary</span>
            </div>
            <div class="summary-line">
              <span class="summary-label">Estimated taxes</span>
              <span class="summary-value">$8</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-line summary-total">
              <span class="summary-label">Total</span>
              <span class="summary-value total">$105</span>
            </div>
          </div>
          <a class="button primary checkout-btn" href="/online-sp/checkout.php">
            Proceed to Checkout
            <span>→</span>
          </a>
          <div class="cart-security">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <span>Secure checkout with SSL encryption</span>
          </div>
        </aside>
      </section>
    </main>
<?php include __DIR__ . '/includes/footer.php'; ?>

