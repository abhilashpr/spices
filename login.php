<?php
$pageTitle = 'Login | Saffron & Spice';
include __DIR__ . '/includes/header.php';
?>
    <main class="page-main">
      <section class="auth-main">
        <article class="glass-panel auth-card">
          <span class="tag">Welcome back</span>
          <h2>Sign in to your atelier</h2>
          <form class="form-stack" novalidate>
            <div class="input-field">
              <label for="login-email">Email</label>
              <input type="email" id="login-email" name="login-email" placeholder="chef@atelier.com" />
            </div>
            <div class="input-field">
              <label for="login-password">Password</label>
              <input type="password" id="login-password" name="login-password" placeholder="••••••••" />
            </div>
            <div class="summary-line">
              <label class="auth-switch">
                <input type="checkbox" /> Remember me
              </label>
              <a href="#" class="auth-switch">Forgot password?</a>
            </div>
            <button class="button primary" type="submit">Login</button>
          </form>
          <p class="auth-switch">
            New to Saffron &amp; Spice? <a href="/online-sp/register.php">Create an account</a>
          </p>
        </article>
        <article class="glass-panel auth-card">
          <span class="tag">Membership perks</span>
          <h2>Join the Taste Club</h2>
          <ul class="form-stack">
            <li>Exclusive access to limited harvest drops.</li>
            <li>Chef-led live tastings and pairing workshops.</li>
            <li>Seasonal recipe booklets and ingredient stories.</li>
          </ul>
          <a class="button outline" href="/online-sp/index.php#taste-club">Discover membership</a>
        </article>
      </section>
    </main>
<?php include __DIR__ . '/includes/footer.php'; ?>

