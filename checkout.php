<?php
$pageTitle = 'Checkout | Saffron & Spice';
include __DIR__ . '/includes/header.php';
?>
    <main class="checkout-page-main">
      <section class="checkout-main">
        <div class="checkout-left">
          <form class="checkout-form" id="checkout-form" novalidate>
            <div class="form-section">
              <h2 class="form-section-title">Delivery Details</h2>
              <div class="form-stack">
                <div class="input-field">
                  <label for="full-name">Full name</label>
                  <input type="text" id="full-name" name="full-name" placeholder="Anita Kapoor" required />
                </div>
                <div class="input-row">
                  <div class="input-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="chef@atelier.com" required />
                  </div>
                  <div class="input-field">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" placeholder="(+91) 98765 43210" required />
                  </div>
                </div>
              </div>
            </div>

            <div class="form-section">
              <h2 class="form-section-title">Shipping Address</h2>
              <div class="form-stack">
                <div class="input-field">
                  <label for="address">Street Address</label>
                  <input type="text" id="address" name="address" placeholder="121 Spice Lane" required />
                </div>
                <div class="input-row">
                  <div class="input-field">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" placeholder="Jaipur" required />
                  </div>
                  <div class="input-field">
                    <label for="postal">Postal Code</label>
                    <input type="text" id="postal" name="postal" placeholder="302001" required />
                  </div>
                </div>
                <div class="input-field">
                  <label for="notes">Delivery Notes (Optional)</label>
                  <textarea id="notes" name="notes" placeholder="Preferred delivery window or special instructions" rows="3"></textarea>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="checkout-right">
          <div class="checkout-sidebar">
            <div class="order-overview-section">
              <h2 class="sidebar-title">Order Overview</h2>
              <div class="order-items">
                <div class="order-item">
                  <div class="order-item-info">
                    <span class="order-item-name">Amber Garam</span>
                    <span class="order-item-qty">2 × $34</span>
                  </div>
                  <span class="order-item-price">$68</span>
                </div>
                <div class="order-item">
                  <div class="order-item-info">
                    <span class="order-item-name">Charred Citrus Harissa</span>
                    <span class="order-item-qty">1 × $29</span>
                  </div>
                  <span class="order-item-price">$29</span>
                </div>
              </div>
              <div class="order-summary-divider"></div>
              <div class="order-summary">
                <div class="summary-line">
                  <span class="summary-label">Subtotal</span>
                  <span class="summary-value">$97</span>
                </div>
                <div class="summary-line">
                  <span class="summary-label">Shipping</span>
                  <span class="summary-value free">Complimentary</span>
                </div>
                <div class="summary-line">
                  <span class="summary-label">Tax</span>
                  <span class="summary-value">$8</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-line summary-total">
                  <span class="summary-label">Total</span>
                  <span class="summary-value total">$105</span>
                </div>
              </div>
            </div>

            <div class="payment-section">
              <h2 class="sidebar-title">Payment</h2>
              <div class="form-stack">
                <div class="input-field">
                  <label for="card-number">Card Number</label>
                  <input type="text" id="card-number" name="card-number" placeholder="0000 0000 0000 0000" maxlength="19" required />
                </div>
                <div class="input-row">
                  <div class="input-field">
                    <label for="expiry">Expiry Date</label>
                    <input type="text" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5" required />
                  </div>
                  <div class="input-field">
                    <label for="cvc">CVC</label>
                    <input type="text" id="cvc" name="cvc" placeholder="123" maxlength="4" required />
                  </div>
                </div>
                <div class="input-field">
                  <label for="card-name">Cardholder Name</label>
                  <input type="text" id="card-name" name="card-name" placeholder="Name on card" required />
                </div>
              </div>
            </div>

            <button class="button primary checkout-submit" type="submit" form="checkout-form">
              Place Order
              <span>→</span>
            </button>

            <div class="checkout-security">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
              <span>Secure checkout with SSL encryption</span>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php include __DIR__ . '/includes/footer.php'; ?>

