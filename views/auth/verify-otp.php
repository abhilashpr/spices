<?php
/**
 * OTP Verification View
 */
require_once __DIR__ . '/../../app/helpers/helpers.php';
?>
<section class="login-page-wrapper">
  <!-- Animated Background Elements -->
  <div class="login-background">
    <div class="floating-spice floating-spice-1"></div>
    <div class="floating-spice floating-spice-2"></div>
    <div class="floating-spice floating-spice-3"></div>
    <div class="floating-spice floating-spice-4"></div>
  </div>

  <div class="login-container" style="max-width: 500px; grid-template-columns: 1fr;">
    <div class="login-card">
      <!-- Verification Section -->
      <div class="login-header">
        <div class="login-icon-wrapper">
          <svg class="login-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
        <h1 class="login-title">Verify Your Email</h1>
        <p class="login-subtitle">We've sent a 6-digit code to<br><strong><?= e($email ?? '') ?></strong></p>
      </div>

      <?php if (isset($flash) && $flash): ?>
        <div class="login-alert login-alert-<?= $flash['type'] === 'error' ? 'error' : 'info' ?>">
          <span class="alert-icon"><?= $flash['type'] === 'error' ? '⚠' : 'ℹ' ?></span>
          <span><?= e($flash['message']) ?></span>
        </div>
      <?php endif; ?>
      
      <form method="POST" action="<?= url('verify-otp') ?>" class="login-form" id="otpForm">
        <input type="hidden" name="email" value="<?= e($email ?? '') ?>">
        
        <div class="form-group">
          <div class="input-wrapper">
            <input 
              type="text" 
              id="otp" 
              name="otp" 
              class="form-input otp-input"
              placeholder=" " 
              required 
              maxlength="6"
              pattern="[0-9]{6}"
              autocomplete="off"
              inputmode="numeric"
              style="text-align: center; font-size: 1.5rem; letter-spacing: 8px; font-weight: bold;"
            />
            <label for="otp" class="form-label">
              <span class="label-icon">🔐</span>
              <span class="label-text">Enter 6-Digit OTP</span>
            </label>
            <div class="input-line"></div>
          </div>
        </div>

        <button type="submit" class="login-btn" id="verifyBtn">
          <span class="btn-text">Verify & Continue</span>
          <span class="btn-loader"></span>
          <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </button>

        <div class="otp-actions">
          <p class="otp-help-text">
            Didn't receive the code? 
            <button type="button" class="resend-link" id="resendOTP">Resend OTP</button>
          </p>
          <p class="otp-timer" id="otpTimer" style="margin-top: 10px; color: rgba(9, 52, 42, 0.6); font-size: 0.85rem;">
            You can resend OTP in <span id="timer">60</span> seconds
          </p>
        </div>

        <p class="login-footer">
          Wrong email? 
          <a href="<?= url('register') ?>" class="register-link">Go back</a>
        </p>
      </form>
    </div>
  </div>
</section>

<style>
/* Include login page styles */
<?php include __DIR__ . '/login.php'; ?>
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // OTP input - auto format and move to next
  const otpInput = document.getElementById('otp');
  if (otpInput) {
    otpInput.addEventListener('input', function(e) {
      // Only allow numbers
      this.value = this.value.replace(/[^0-9]/g, '');
      
      // Auto submit when 6 digits entered
      if (this.value.length === 6) {
        // Small delay for better UX
        setTimeout(() => {
          document.getElementById('otpForm').submit();
        }, 300);
      }
    });

    otpInput.addEventListener('paste', function(e) {
      e.preventDefault();
      const paste = (e.clipboardData || window.clipboardData).getData('text');
      const numbers = paste.replace(/[^0-9]/g, '').trim().substring(0, 6);
      this.value = numbers;
      if (numbers.length === 6) {
        setTimeout(() => {
          document.getElementById('otpForm').submit();
        }, 300);
      }
    });
  }

  // Resend OTP with timer
  let resendTimer = 60;
  const resendBtn = document.getElementById('resendOTP');
  const timerEl = document.getElementById('timer');
  const timerContainer = document.getElementById('otpTimer');
  
  function updateTimer() {
    if (resendTimer > 0) {
      timerEl.textContent = resendTimer;
      resendTimer--;
      setTimeout(updateTimer, 1000);
    } else {
      resendBtn.style.pointerEvents = 'auto';
      resendBtn.style.opacity = '1';
      timerContainer.style.display = 'none';
    }
  }
  
  if (resendBtn) {
    resendBtn.style.pointerEvents = 'none';
    resendBtn.style.opacity = '0.5';
    resendBtn.addEventListener('click', function() {
      const email = document.querySelector('input[name="email"]').value;
      
      fetch('<?= url("resend-otp") ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'email=' + encodeURIComponent(email)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('OTP has been resent to your email!');
          resendTimer = 60;
          resendBtn.style.pointerEvents = 'none';
          resendBtn.style.opacity = '0.5';
          timerContainer.style.display = 'block';
          updateTimer();
        } else {
          alert(data.message || 'Failed to resend OTP');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Failed to resend OTP. Please try again.');
      });
    });
    
    updateTimer();
  }

  // Form submission
  const otpForm = document.getElementById('otpForm');
  const verifyBtn = document.getElementById('verifyBtn');
  
  if (otpForm && verifyBtn) {
    otpForm.addEventListener('submit', function(e) {
      verifyBtn.classList.add('loading');
      verifyBtn.disabled = true;
    });
  }
});
</script>

