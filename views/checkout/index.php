<?php
/**
 * Checkout View
 */
require_once __DIR__ . '/../../app/helpers/helpers.php';

$isLoggedIn = $isLoggedIn ?? false;
$user = $user ?? null;
$addresses = $addresses ?? [];
$defaultAddress = $defaultAddress ?? null;
$cart = $cart ?? [];
$subtotal = $subtotal ?? 0;
$shipping = $shipping ?? 0;
$tax = $tax ?? 0;
$total = $total ?? 0;
?>
<section class="checkout-page-main">
  <div class="container">
    <div class="checkout-header">
      <h1>Checkout</h1>
      <p>Complete your order</p>
    </div>

    <?php if (empty($cart)): ?>
      <div class="empty-cart-message">
        <div class="empty-icon">🛒</div>
        <h2>Your cart is empty</h2>
        <p>Add items to your cart to proceed with checkout.</p>
        <a href="<?= url('categories') ?>" class="btn-browse">Browse Products</a>
      </div>
    <?php else: ?>
      <div class="checkout-content">
        <div class="checkout-left">
          <!-- Guest Checkout Section -->
          <?php if (!$isLoggedIn): ?>
            <div class="checkout-section guest-checkout-section" id="guest-checkout-section">
              <h2 class="section-title">Guest Checkout</h2>
              <div class="form-stack">
                <div class="input-field">
                  <label for="guest-email">Email Address</label>
                  <input type="email" id="guest-email" name="guest-email" placeholder="your@email.com" required />
                  <p class="field-hint">We'll send you an OTP to verify your email</p>
                </div>
                <button type="button" class="btn-primary" id="send-otp-btn">
                  Send OTP
                </button>
              </div>

              <div class="otp-verification-section" id="otp-verification-section" style="display: none;">
                <div class="form-stack">
                  <div class="input-field">
                    <label for="guest-otp">Enter OTP</label>
                    <input type="text" id="guest-otp" name="guest-otp" placeholder="000000" maxlength="6" required />
                    <p class="field-hint">Check your email for the 6-digit OTP</p>
                  </div>
                  <button type="button" class="btn-primary" id="verify-otp-btn">
                    Verify OTP
                  </button>
                  <button type="button" class="btn-link" id="resend-otp-btn">
                    Resend OTP
                  </button>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <!-- User Details Section (for logged in users) -->
          <?php if ($isLoggedIn && $user): ?>
            <div class="checkout-section user-details-section">
              <h2 class="section-title">
                <?php 
                  $userName = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
                  if (empty($userName)) {
                    $userName = $user['name'] ?? $user['email'] ?? 'User';
                  }
                  echo e($userName);
                ?>
              </h2>
              <div class="form-stack">
                <div class="input-row">
                  <div class="input-field">
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" value="<?= e($user['firstname'] ?? '') ?>" required />
                  </div>
                  <div class="input-field">
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" value="<?= e($user['lastname'] ?? '') ?>" required />
                  </div>
                </div>
                <div class="input-row">
                  <div class="input-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= e($user['email'] ?? '') ?>" required />
                  </div>
                  <div class="input-field">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?= e($user['phone'] ?? '') ?>" required />
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <!-- Shipping Address Section -->
          <div class="checkout-section shipping-address-section">
            <div class="section-header">
              <h2 class="section-title">Shipping Address</h2>
              <?php if ($isLoggedIn): ?>
                <button type="button" class="btn-add-address" id="add-address-btn">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                  </svg>
                  Add New Address
                </button>
              <?php endif; ?>
            </div>

            <?php if ($isLoggedIn && !empty($addresses)): ?>
              <!-- Address Selection -->
              <div class="address-list" id="address-list">
                <?php foreach ($addresses as $address): ?>
                  <div class="address-card <?= ($address['is_default'] == 1) ? 'default' : '' ?>" data-address-id="<?= e($address['id']) ?>">
                    <div class="address-radio">
                      <input type="radio" name="selected_address" value="<?= e($address['id']) ?>" 
                             id="address-<?= e($address['id']) ?>" 
                             <?= ($address['is_default'] == 1) ? 'checked' : '' ?> />
                      <label for="address-<?= e($address['id']) ?>"></label>
                    </div>
                    <div class="address-content">
                      <div class="address-header">
                        <h3><?= e($address['address_line1']) ?></h3>
                        <?php if ($address['is_default'] == 1): ?>
                          <span class="default-badge">Default</span>
                        <?php endif; ?>
                      </div>
                      <p class="address-line"><?= e($address['address_line2'] ?? '') ?></p>
                      <p class="address-details">
                        <?= e($address['city']) ?>, <?= e($address['state']) ?> - <?= e($address['post_code']) ?>
                      </p>
                      <?php if ($address['landmark']): ?>
                        <p class="address-landmark">Landmark: <?= e($address['landmark']) ?></p>
                      <?php endif; ?>
                      <?php if ($address['note']): ?>
                        <p class="address-note"><?= e($address['note']) ?></p>
                      <?php endif; ?>
                      <div class="address-actions">
                        <button type="button" class="btn-edit-address" data-address-id="<?= e($address['id']) ?>">Edit</button>
                        <button type="button" class="btn-delete-address" data-address-id="<?= e($address['id']) ?>">Delete</button>
                        <?php if ($address['is_default'] != 1): ?>
                          <button type="button" class="btn-set-default" data-address-id="<?= e($address['id']) ?>">Set as Default</button>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <!-- Address Form (for new address or guest) -->
            <div class="address-form" id="address-form" <?= ($isLoggedIn && !empty($addresses)) ? 'style="display: none;"' : '' ?>>
              <div class="form-stack">
                <div class="input-row">
                  <div class="input-field">
                    <label for="contact_name">Contact Name *</label>
                    <input type="text" id="contact_name" name="contact_name" 
                           value="<?= e($defaultAddress['contact_name'] ?? ($user['name'] ?? '')) ?>" required />
                  </div>
                  <div class="input-field">
                    <label for="contact_email">Contact Email *</label>
                    <input type="email" id="contact_email" name="contact_email" 
                           value="<?= e($defaultAddress['contact_email'] ?? ($user['email'] ?? '')) ?>" required />
                  </div>
                </div>
                <div class="input-field">
                  <label for="contact_phone">Contact Phone *</label>
                  <input type="tel" id="contact_phone" name="contact_phone" 
                         value="<?= e($defaultAddress['contact_phone'] ?? ($user['phone'] ?? '')) ?>" required />
                </div>
                <div class="input-field">
                  <label for="address_line1">Address Line 1 *</label>
                  <input type="text" id="address_line1" name="address_line1" 
                         value="<?= e($defaultAddress['address_line1'] ?? '') ?>" required />
                </div>
                <div class="input-field">
                  <label for="address_line2">Address Line 2</label>
                  <input type="text" id="address_line2" name="address_line2" 
                         value="<?= e($defaultAddress['address_line2'] ?? '') ?>" />
                </div>
                <div class="input-row">
                  <div class="input-field">
                    <label for="city">City *</label>
                    <input type="text" id="city" name="city" 
                           value="<?= e($defaultAddress['city'] ?? '') ?>" required />
                  </div>
                  <div class="input-field">
                    <label for="state">State *</label>
                    <input type="text" id="state" name="state" 
                           value="<?= e($defaultAddress['state'] ?? '') ?>" required />
                  </div>
                </div>
                <div class="input-row">
                  <div class="input-field">
                    <label for="country">Country *</label>
                    <input type="text" id="country" name="country" 
                           value="<?= e($defaultAddress['country'] ?? 'India') ?>" required />
                  </div>
                  <div class="input-field">
                    <label for="post_code">Postal Code *</label>
                    <input type="text" id="post_code" name="post_code" 
                           value="<?= e($defaultAddress['post_code'] ?? '') ?>" required />
                  </div>
                </div>
                <div class="input-field">
                  <label for="landmark">Landmark</label>
                  <input type="text" id="landmark" name="landmark" 
                         value="<?= e($defaultAddress['landmark'] ?? '') ?>" />
                </div>
                <div class="input-field">
                  <label for="address_note">Delivery Notes</label>
                  <textarea id="address_note" name="address_note" rows="3" 
                            placeholder="Any special delivery instructions"><?= e($defaultAddress['note'] ?? '') ?></textarea>
                </div>
                <?php if ($isLoggedIn): ?>
                  <div class="input-field checkbox-field">
                    <label>
                      <input type="checkbox" id="set_as_default" name="set_as_default" />
                      Set as default address
                    </label>
                  </div>
                  <button type="button" class="btn-primary" id="save-address-btn">Save Address</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="checkout-right">
          <div class="checkout-sidebar">
            <!-- Order Summary -->
            <div class="order-summary-section">
              <h2 class="sidebar-title">Order Summary</h2>
              <div class="order-items">
                <?php foreach ($cart as $item): ?>
                  <?php
                  $price = $item['offer_price'] ?? $item['price'] ?? 0;
                  $quantity = $item['quantity'] ?? 1;
                  $itemTotal = $price * $quantity;
                  ?>
                  <div class="order-item">
                    <div class="order-item-info">
                      <span class="order-item-name"><?= e($item['product_name'] ?? 'Product') ?></span>
                      <span class="order-item-qty"><?= $quantity ?> × <?= format_price($price) ?></span>
                    </div>
                    <span class="order-item-price"><?= format_price($itemTotal) ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="order-summary-divider"></div>
              <div class="order-summary">
                <div class="summary-line">
                  <span class="summary-label">Subtotal</span>
                  <span class="summary-value"><?= format_price($subtotal) ?></span>
                </div>
                <div class="summary-line">
                  <span class="summary-label">Shipping</span>
                  <span class="summary-value free"><?= $shipping > 0 ? format_price($shipping) : 'Free' ?></span>
                </div>
                <div class="summary-line">
                  <span class="summary-label">Tax (GST)</span>
                  <span class="summary-value"><?= format_price($tax) ?></span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-line summary-total">
                  <span class="summary-label">Total</span>
                  <span class="summary-value total"><?= format_price($total) ?></span>
                </div>
              </div>
            </div>

            <!-- Coupon Code Section -->
            <div class="coupon-section">
              <h3 class="coupon-title">Have a coupon code?</h3>
              <div class="coupon-form">
                <div class="input-field">
                  <input type="text" id="coupon-code" name="coupon_code" placeholder="Enter coupon code" />
                </div>
                <button type="button" class="btn-apply-coupon" id="apply-coupon-btn">Apply</button>
              </div>
              <div class="coupon-message" id="coupon-message"></div>
            </div>

            <!-- Place Order Button -->
            <button class="btn-checkout-submit" type="submit" form="checkout-form" id="place-order-btn">
              Place Order
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
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
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
// Checkout JavaScript functionality
(function() {
  const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;
  
  // Guest Checkout
  if (!isLoggedIn) {
    const sendOtpBtn = document.getElementById('send-otp-btn');
    const verifyOtpBtn = document.getElementById('verify-otp-btn');
    const resendOtpBtn = document.getElementById('resend-otp-btn');
    const guestEmailInput = document.getElementById('guest-email');
    const guestOtpInput = document.getElementById('guest-otp');
    const otpSection = document.getElementById('otp-verification-section');

    if (sendOtpBtn) {
      sendOtpBtn.addEventListener('click', function() {
        const email = guestEmailInput.value.trim();
        if (!email || !email.includes('@')) {
          alert('Please enter a valid email address');
          return;
        }

        sendOtpBtn.disabled = true;
        sendOtpBtn.textContent = 'Sending...';

        fetch('/online-sp/checkout?action=guest-checkout', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            otpSection.style.display = 'block';
            alert(data.message);
          } else {
            if (data.login_required) {
              if (confirm(data.message + ' Would you like to login?')) {
                window.location.href = '/online-sp/login';
              }
            } else {
              alert(data.message);
            }
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Failed to send OTP. Please try again.');
        })
        .finally(() => {
          sendOtpBtn.disabled = false;
          sendOtpBtn.textContent = 'Send OTP';
        });
      });
    }

    if (verifyOtpBtn) {
      verifyOtpBtn.addEventListener('click', function() {
        const email = guestEmailInput.value.trim();
        const otp = guestOtpInput.value.trim().replace(/\D/g, '');

        if (!email || !otp || otp.length !== 6) {
          alert('Please enter a valid 6-digit OTP');
          return;
        }

        verifyOtpBtn.disabled = true;
        verifyOtpBtn.textContent = 'Verifying...';

        fetch('/online-sp/checkout?action=verify-guest-otp', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: email, otp: otp })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(data.message);
            window.location.href = data.redirect || '/online-sp/checkout';
          } else {
            alert(data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Failed to verify OTP. Please try again.');
        })
        .finally(() => {
          verifyOtpBtn.disabled = false;
          verifyOtpBtn.textContent = 'Verify OTP';
        });
      });
    }

    if (resendOtpBtn) {
      resendOtpBtn.addEventListener('click', function() {
        if (sendOtpBtn) {
          sendOtpBtn.click();
        }
      });
    }
  }

  // Helper function to show messages
  function showCheckoutMessage(message, type = 'success') {
    const existingMessage = document.querySelector('.cart-message');
    if (existingMessage) {
      existingMessage.remove();
    }
    const messageEl = document.createElement('div');
    messageEl.className = 'cart-message' + (type === 'error' ? ' error' : ' success');
    messageEl.textContent = message;
    document.body.appendChild(messageEl);
    void messageEl.offsetHeight;
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

  // Address Management (for logged in users)
  if (isLoggedIn) {
    const addAddressBtn = document.getElementById('add-address-btn');
    const addressForm = document.getElementById('address-form');
    const saveAddressBtn = document.getElementById('save-address-btn');
    let editingAddressId = null;

    if (addAddressBtn && addressForm) {
      addAddressBtn.addEventListener('click', function() {
        addressForm.style.display = 'block';
        addressForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        // Reset form
        editingAddressId = null;
        // Reset all form fields manually since there's no form element
        const formFields = ['contact_name', 'contact_email', 'contact_phone', 'address_line1', 'address_line2', 
                           'city', 'state', 'country', 'post_code', 'landmark', 'address_note'];
        formFields.forEach(fieldId => {
          const field = document.getElementById(fieldId);
          if (field) {
            field.value = '';
          }
        });
        const defaultCheckbox = document.getElementById('set_as_default');
        if (defaultCheckbox) {
          defaultCheckbox.checked = false;
        }
      });
    }

    // Save Address Button
    if (saveAddressBtn) {
      saveAddressBtn.addEventListener('click', function() {
        if (!addressForm) return;

        // Collect form data
        const addressData = {
          contact_name: document.getElementById('contact_name').value.trim(),
          contact_email: document.getElementById('contact_email').value.trim(),
          contact_phone: document.getElementById('contact_phone').value.trim(),
          address_line1: document.getElementById('address_line1').value.trim(),
          address_line2: document.getElementById('address_line2').value.trim(),
          city: document.getElementById('city').value.trim(),
          state: document.getElementById('state').value.trim(),
          country: document.getElementById('country').value.trim(),
          post_code: document.getElementById('post_code').value.trim(),
          landmark: document.getElementById('landmark').value.trim(),
          address_note: document.getElementById('address_note').value.trim(),
          set_as_default: document.getElementById('set_as_default').checked
        };

        // Validate required fields
        if (!addressData.contact_name || !addressData.contact_email || !addressData.contact_phone || 
            !addressData.address_line1 || !addressData.city || !addressData.state || !addressData.country || !addressData.post_code) {
          showCheckoutMessage('Please fill in all required fields', 'error');
          return;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(addressData.contact_email)) {
          showCheckoutMessage('Please enter a valid email address', 'error');
          return;
        }

        // Disable button during request
        saveAddressBtn.disabled = true;
        saveAddressBtn.textContent = 'Saving...';

        const url = editingAddressId 
          ? '<?= url('checkout/update-address') ?>'
          : '<?= url('checkout/add-address') ?>';

        if (editingAddressId) {
          addressData.address_id = editingAddressId;
        }

        fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(addressData)
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          if (data.success) {
            showCheckoutMessage(data.message, 'success');
            // Reload page to show updated addresses
            setTimeout(() => {
              window.location.reload();
            }, 1000);
          } else {
            const errorMsg = data.message || 'Failed to save address. Please try again.';
            console.error('Address save error:', data);
            showCheckoutMessage(errorMsg, 'error');
            saveAddressBtn.disabled = false;
            saveAddressBtn.textContent = 'Save Address';
          }
        })
        .catch(error => {
          console.error('Address save error:', error);
          showCheckoutMessage('Network error: ' + error.message + '. Please check your connection and try again.', 'error');
          saveAddressBtn.disabled = false;
          saveAddressBtn.textContent = 'Save Address';
        });
      });
    }

    // Edit Address Button
    document.querySelectorAll('.btn-edit-address').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const addressId = this.dataset.addressId;
        if (!addressId) return;

        // Fetch address details and populate form
        const addressCard = this.closest('.address-card');
        if (addressCard) {
          // Get address data from the card (or fetch from server)
          const address = <?= json_encode($addresses ?? []) ?>.find(a => a.id == addressId);
          if (address) {
            editingAddressId = addressId;
            addressForm.style.display = 'block';
            addressForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            
            // Populate form
            document.getElementById('contact_name').value = address.contact_name || '';
            document.getElementById('contact_email').value = address.contact_email || '';
            document.getElementById('contact_phone').value = address.contact_phone || '';
            document.getElementById('address_line1').value = address.address_line1 || '';
            document.getElementById('address_line2').value = address.address_line2 || '';
            document.getElementById('city').value = address.city || '';
            document.getElementById('state').value = address.state || '';
            document.getElementById('country').value = address.country || 'India';
            document.getElementById('post_code').value = address.post_code || '';
            document.getElementById('landmark').value = address.landmark || '';
            document.getElementById('address_note').value = address.note || '';
            document.getElementById('set_as_default').checked = address.is_default == 1;
          }
        }
      });
    });

    // Delete Address Button
    document.querySelectorAll('.btn-delete-address').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const addressId = this.dataset.addressId;
        if (!addressId) return;

        if (!confirm('Are you sure you want to delete this address?')) {
          return;
        }

        const btn = this;
        btn.disabled = true;
        btn.style.opacity = '0.6';

        fetch('<?= url('checkout/delete-address') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ address_id: addressId })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showCheckoutMessage(data.message, 'success');
            setTimeout(() => {
              window.location.reload();
            }, 1000);
          } else {
            showCheckoutMessage(data.message, 'error');
            btn.disabled = false;
            btn.style.opacity = '1';
          }
        })
        .catch(error => {
          console.error('Address delete error:', error);
          showCheckoutMessage('An error occurred. Please try again.', 'error');
          btn.disabled = false;
          btn.style.opacity = '1';
        });
      });
    });

    // Set Default Address Button
    document.querySelectorAll('.btn-set-default').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const addressId = this.dataset.addressId;
        if (!addressId) return;

        const btn = this;
        btn.disabled = true;
        btn.style.opacity = '0.6';

        fetch('<?= url('checkout/set-default-address') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ address_id: addressId })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showCheckoutMessage(data.message, 'success');
            setTimeout(() => {
              window.location.reload();
            }, 1000);
          } else {
            showCheckoutMessage(data.message, 'error');
            btn.disabled = false;
            btn.style.opacity = '1';
          }
        })
        .catch(error => {
          console.error('Set default address error:', error);
          showCheckoutMessage('An error occurred. Please try again.', 'error');
          btn.disabled = false;
          btn.style.opacity = '1';
        });
      });
    });

    // Address selection
    document.querySelectorAll('input[name="selected_address"]').forEach(radio => {
      radio.addEventListener('change', function() {
        // Address selection is handled by radio buttons
        console.log('Selected address:', this.value);
      });
    });
  } else {
    // Guest user - save address to session
    const addressForm = document.getElementById('address-form');
    if (addressForm) {
      // Add a save button for guest users if not exists
      const formStack = addressForm.querySelector('.form-stack');
      if (formStack && !formStack.querySelector('#save-guest-address-btn')) {
        const saveBtn = document.createElement('button');
        saveBtn.type = 'button';
        saveBtn.id = 'save-guest-address-btn';
        saveBtn.className = 'btn-primary';
        saveBtn.textContent = 'Save Address';
        formStack.appendChild(saveBtn);

        saveBtn.addEventListener('click', function() {
          const addressData = {
            contact_name: document.getElementById('contact_name').value.trim(),
            contact_email: document.getElementById('contact_email').value.trim(),
            contact_phone: document.getElementById('contact_phone').value.trim(),
            address_line1: document.getElementById('address_line1').value.trim(),
            address_line2: document.getElementById('address_line2').value.trim(),
            city: document.getElementById('city').value.trim(),
            state: document.getElementById('state').value.trim(),
            country: document.getElementById('country').value.trim(),
            post_code: document.getElementById('post_code').value.trim(),
            landmark: document.getElementById('landmark').value.trim(),
            address_note: document.getElementById('address_note').value.trim()
          };

          // Validate required fields
          if (!addressData.contact_name || !addressData.contact_email || !addressData.contact_phone || 
              !addressData.address_line1 || !addressData.city || !addressData.state || !addressData.country || !addressData.post_code) {
            showCheckoutMessage('Please fill in all required fields', 'error');
            return;
          }

          // Validate email format
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(addressData.contact_email)) {
            showCheckoutMessage('Please enter a valid email address', 'error');
            return;
          }

          saveBtn.disabled = true;
          saveBtn.textContent = 'Saving...';

          fetch('<?= url('checkout/save-guest-address') ?>', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(addressData)
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showCheckoutMessage(data.message, 'success');
              saveBtn.disabled = false;
              saveBtn.textContent = 'Address Saved';
            } else {
              showCheckoutMessage(data.message, 'error');
              saveBtn.disabled = false;
              saveBtn.textContent = 'Save Address';
            }
          })
          .catch(error => {
            console.error('Guest address save error:', error);
            showCheckoutMessage('An error occurred. Please try again.', 'error');
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Address';
          });
        });
      }
    }
  }

  // Coupon Code
  const applyCouponBtn = document.getElementById('apply-coupon-btn');
  if (applyCouponBtn) {
    applyCouponBtn.addEventListener('click', function() {
      const couponCode = document.getElementById('coupon-code').value.trim();
      const couponMessage = document.getElementById('coupon-message');
      
      if (!couponCode) {
        couponMessage.textContent = 'Please enter a coupon code';
        couponMessage.className = 'coupon-message error';
        return;
      }

      // TODO: Implement coupon validation
      couponMessage.textContent = 'Coupon code applied successfully!';
      couponMessage.className = 'coupon-message success';
    });
  }
})();
</script>
