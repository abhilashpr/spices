<?php
/**
 * Login View - Interactive & Engaging Design
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

  <div class="login-container">
    <div class="login-card">
      <!-- Welcome Section -->
      <div class="login-header">
        <div class="login-icon-wrapper">
          <svg class="login-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <h1 class="login-title">Welcome Back</h1>
        <p class="login-subtitle">Sign in to continue your spice journey</p>
      </div>

      <?php if (isset($flash) && $flash): ?>
        <div class="login-alert login-alert-<?= $flash['type'] === 'error' ? 'error' : 'info' ?>">
          <span class="alert-icon"><?= $flash['type'] === 'error' ? '⚠' : 'ℹ' ?></span>
          <span><?= e($flash['message']) ?></span>
        </div>
      <?php endif; ?>
      
      <form method="POST" action="" class="login-form" id="loginForm">
        <div class="form-group">
          <div class="input-wrapper">
            <input 
              type="email" 
              id="email" 
              name="email" 
              class="form-input"
              placeholder=" " 
              required 
              autocomplete="email"
            />
            <label for="email" class="form-label">
              <span class="label-icon">✉</span>
              <span class="label-text">Email Address</span>
            </label>
            <div class="input-line"></div>
          </div>
        </div>

        <div class="form-group">
          <div class="input-wrapper">
            <input 
              type="password" 
              id="password" 
              name="password" 
              class="form-input"
              placeholder=" " 
              required 
              autocomplete="current-password"
            />
            <label for="password" class="form-label">
              <span class="label-icon">🔒</span>
              <span class="label-text">Password</span>
            </label>
            <div class="input-line"></div>
            <button type="button" class="password-toggle" id="passwordToggle" aria-label="Toggle password visibility">
              <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
          </div>
        </div>

        <div class="form-options">
          <label class="checkbox-wrapper">
            <input type="checkbox" name="remember" id="remember" />
            <span class="checkbox-custom"></span>
            <span class="checkbox-label">Remember me</span>
          </label>
          <a href="#" class="forgot-link">Forgot Password?</a>
        </div>

        <button type="submit" class="login-btn" id="loginBtn">
          <span class="btn-text">Sign In</span>
          <span class="btn-loader"></span>
          <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>
        </button>

        <div class="login-divider">
          <span>or</span>
        </div>

        <div class="social-login">
          <button type="button" class="social-btn social-google">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
              <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
              <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
              <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            <span>Continue with Google</span>
          </button>
        </div>

        <p class="login-footer">
          Don't have an account? 
          <a href="<?= url('register') ?>" class="register-link">Create one now</a>
        </p>
      </form>
    </div>

    <!-- Side Decoration -->
    <div class="login-decoration">
      <div class="decoration-content">
        <h2>Premium Spice Atelier</h2>
        <p>Discover authentic flavors from around the world</p>
        <ul class="feature-list">
          <li>✨ Exclusive member discounts</li>
          <li>🚚 Free shipping on orders</li>
          <li>🎁 Early access to new blends</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<style>
/* Login Page Styles */
.login-page-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 30px 20px;
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 50%, #f1f8f4 100%);
}

.login-background {
  position: absolute;
  inset: 0;
  overflow: hidden;
  z-index: 0;
}

.floating-spice {
  position: absolute;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(50, 198, 141, 0.15), rgba(50, 198, 141, 0.05));
  backdrop-filter: blur(10px);
  animation: float 20s infinite ease-in-out;
}

.floating-spice-1 {
  top: 10%;
  left: 10%;
  animation-delay: 0s;
  width: 100px;
  height: 100px;
}

.floating-spice-2 {
  top: 60%;
  right: 15%;
  animation-delay: -5s;
  width: 60px;
  height: 60px;
}

.floating-spice-3 {
  bottom: 20%;
  left: 20%;
  animation-delay: -10s;
  width: 120px;
  height: 120px;
}

.floating-spice-4 {
  top: 30%;
  right: 30%;
  animation-delay: -15s;
  width: 70px;
  height: 70px;
}

@keyframes float {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  25% { transform: translate(30px, -30px) rotate(90deg); }
  50% { transform: translate(-20px, 20px) rotate(180deg); }
  75% { transform: translate(20px, 30px) rotate(270deg); }
}

.login-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  max-width: 1000px;
  width: 100%;
  gap: 30px;
  position: relative;
  z-index: 1;
}

.login-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 32px 32px;
  box-shadow: 0 20px 60px rgba(21, 66, 55, 0.15), 0 0 0 1px rgba(50, 198, 141, 0.1);
  transform: translateY(0);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  max-height: 90vh;
  overflow-y: auto;
}

.login-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 25px 70px rgba(21, 66, 55, 0.2), 0 0 0 1px rgba(50, 198, 141, 0.15);
}

.login-header {
  text-align: center;
  margin-bottom: 28px;
}

.login-icon-wrapper {
  width: 64px;
  height: 64px;
  margin: 0 auto 16px;
  background: linear-gradient(135deg, #32c68d, #28a870);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 6px 20px rgba(50, 198, 141, 0.3);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); box-shadow: 0 8px 24px rgba(50, 198, 141, 0.3); }
  50% { transform: scale(1.05); box-shadow: 0 12px 32px rgba(50, 198, 141, 0.4); }
}

.login-icon {
  width: 32px;
  height: 32px;
  color: white;
}

.login-title {
  font-family: 'Playfair Display', serif;
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--text-primary, #09342a);
  margin: 0 0 6px 0;
}

.login-subtitle {
  font-size: 0.9rem;
  color: rgba(9, 52, 42, 0.6);
  margin: 0;
}

.login-alert {
  padding: 12px 16px;
  border-radius: 12px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.85rem;
  animation: slideDown 0.3s ease;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.login-alert-error {
  background: rgba(220, 53, 69, 0.1);
  border: 1px solid rgba(220, 53, 69, 0.3);
  color: #dc3545;
}

.login-alert-info {
  background: rgba(50, 198, 141, 0.1);
  border: 1px solid rgba(50, 198, 141, 0.3);
  color: #32c68d;
}

.alert-icon {
  font-size: 1.2rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.form-group {
  position: relative;
}

.input-wrapper {
  position: relative;
}

.form-input {
  width: 100%;
  padding: 18px 48px 6px 48px;
  font-size: 0.95rem;
  border: 2px solid rgba(50, 198, 141, 0.2);
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.8);
  transition: all 0.3s ease;
  outline: none;
  box-sizing: border-box;
  height: 54px;
}

.form-input::placeholder {
  opacity: 0;
  transition: opacity 0.3s ease;
}

.form-input:focus::placeholder {
  opacity: 0.5;
}

.form-input:focus {
  border-color: #32c68d;
  background: white;
  box-shadow: 0 0 0 4px rgba(50, 198, 141, 0.1);
  padding-top: 22px;
  padding-bottom: 6px;
}

.form-input:focus + .form-label,
.form-input.has-value + .form-label,
.input-wrapper:has(.form-input.has-value) .form-label,
.input-wrapper:has(.form-input:focus) .form-label {
  transform: translateY(-38px) translateX(-36px) scale(0.85);
  color: #32c68d;
  font-weight: 600;
}

.form-input:not(:placeholder-shown) {
  padding-top: 22px;
  padding-bottom: 6px;
}

.form-label {
  position: absolute;
  left: 48px;
  top: 18px;
  display: flex;
  align-items: center;
  gap: 8px;
  color: rgba(9, 52, 42, 0.5);
  font-size: 0.95rem;
  pointer-events: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  transform-origin: left center;
  white-space: nowrap;
}

.label-icon {
  font-size: 1.1rem;
}

.input-line {
  position: absolute;
  bottom: 0;
  left: 16px;
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, #32c68d, #28a870);
  transition: width 0.3s ease;
  border-radius: 2px;
}

.form-input:focus ~ .input-line {
  width: calc(100% - 32px);
}

.password-toggle {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  color: rgba(9, 52, 42, 0.5);
  transition: color 0.3s ease;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
}

.password-toggle:hover {
  color: #32c68d;
}

.eye-icon {
  width: 20px;
  height: 20px;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.9rem;
}

.checkbox-wrapper {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  user-select: none;
}

.checkbox-wrapper input[type="checkbox"] {
  display: none;
}

.checkbox-custom {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(50, 198, 141, 0.3);
  border-radius: 6px;
  position: relative;
  transition: all 0.3s ease;
}

.checkbox-wrapper input[type="checkbox"]:checked + .checkbox-custom {
  background: #32c68d;
  border-color: #32c68d;
}

.checkbox-wrapper input[type="checkbox"]:checked + .checkbox-custom::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 14px;
  font-weight: bold;
}

.checkbox-label {
  color: rgba(9, 52, 42, 0.7);
}

.forgot-link {
  color: #32c68d;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.forgot-link:hover {
  color: #28a870;
  text-decoration: underline;
}

.login-btn {
  width: 100%;
  padding: 14px 20px;
  background: linear-gradient(135deg, #32c68d, #28a870);
  color: white;
  border: none;
  border-radius: 14px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(50, 198, 141, 0.3);
  margin-top: 4px;
}

.login-btn::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #28a870, #32c68d);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.login-btn:hover::before {
  opacity: 1;
}

.login-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(50, 198, 141, 0.4);
}

.login-btn:active {
  transform: translateY(0);
}

.btn-text {
  position: relative;
  z-index: 1;
}

.btn-loader {
  display: none;
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  position: relative;
  z-index: 1;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.login-btn.loading .btn-text {
  display: none;
}

.login-btn.loading .btn-loader {
  display: block;
}

.btn-icon {
  width: 20px;
  height: 20px;
  position: relative;
  z-index: 1;
  transition: transform 0.3s ease;
}

.login-btn:hover .btn-icon {
  transform: translateX(4px);
}

.login-divider {
  display: flex;
  align-items: center;
  text-align: center;
  margin: 6px 0;
  color: rgba(9, 52, 42, 0.4);
  font-size: 0.85rem;
}

.login-divider::before,
.login-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: rgba(50, 198, 141, 0.2);
}

.login-divider span {
  padding: 0 16px;
}

.social-login {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.social-btn {
  width: 100%;
  padding: 12px 18px;
  border: 2px solid rgba(50, 198, 141, 0.2);
  border-radius: 14px;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--text-primary, #09342a);
  cursor: pointer;
  transition: all 0.3s ease;
}

.social-btn:hover {
  border-color: #32c68d;
  background: rgba(50, 198, 141, 0.05);
  transform: translateY(-2px);
}

.social-btn svg {
  width: 20px;
  height: 20px;
}

.login-footer {
  text-align: center;
  font-size: 0.85rem;
  color: rgba(9, 52, 42, 0.6);
  margin-top: 6px;
}

.register-link {
  color: #32c68d;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.register-link:hover {
  color: #28a870;
  text-decoration: underline;
}

.login-decoration {
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(50, 198, 141, 0.1), rgba(40, 168, 112, 0.15));
  border-radius: 24px;
  padding: 40px 32px;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(50, 198, 141, 0.2);
  max-height: 90vh;
  overflow-y: auto;
}

.decoration-content {
  text-align: center;
}

.decoration-content h2 {
  font-family: 'Playfair Display', serif;
  font-size: 2rem;
  color: var(--text-primary, #09342a);
  margin: 0 0 12px 0;
}

.decoration-content > p {
  font-size: 0.95rem;
  color: rgba(9, 52, 42, 0.7);
  margin-bottom: 24px;
}

.feature-list {
  list-style: none;
  padding: 0;
  margin: 0;
  text-align: left;
  display: inline-block;
}

.feature-list li {
  padding: 8px 0;
  font-size: 0.9rem;
  color: rgba(9, 52, 42, 0.8);
  display: flex;
  align-items: center;
  gap: 10px;
}

@media (max-width: 968px) {
  .login-container {
    grid-template-columns: 1fr;
  }
  
  .login-decoration {
    display: none;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Password toggle functionality
  const passwordToggle = document.getElementById('passwordToggle');
  const passwordInput = document.getElementById('password');
  
  if (passwordToggle && passwordInput) {
    passwordToggle.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      const eyeIcon = this.querySelector('.eye-icon');
      if (type === 'text') {
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
      } else {
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
      }
    });
  }

  // Form submission with loading state
  const loginForm = document.getElementById('loginForm');
  const loginBtn = document.getElementById('loginBtn');
  
  if (loginForm && loginBtn) {
    loginForm.addEventListener('submit', function(e) {
      loginBtn.classList.add('loading');
      loginBtn.disabled = true;
      
      // Re-enable after 3 seconds if form doesn't submit (fallback)
      setTimeout(function() {
        loginBtn.classList.remove('loading');
        loginBtn.disabled = false;
      }, 3000);
    });
  }

  // Input animations - check if has value
  const inputs = document.querySelectorAll('.form-input');
  inputs.forEach(input => {
    // Check initial value
    if (input.value) {
      input.classList.add('has-value');
    }
    
    input.addEventListener('input', function() {
      if (this.value) {
        this.classList.add('has-value');
      } else {
        this.classList.remove('has-value');
      }
    });
    
    input.addEventListener('focus', function() {
      this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
      this.parentElement.classList.remove('focused');
      if (!this.value) {
        this.classList.remove('has-value');
      }
    });
  });
});
</script>
