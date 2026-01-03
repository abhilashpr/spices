<?php
$pageTitle = 'Register | Saffron & Spice';
include __DIR__ . '/includes/header.php';
?>
    <main class="page-main">
      <section class="auth-main">
        <article class="glass-panel auth-card">
          <span class="tag">Join the atelier</span>
          <h2>Create your account</h2>
          <form class="form-stack" novalidate>
            <div class="input-field">
              <label for="register-name">Full name</label>
              <input type="text" id="register-name" name="register-name" placeholder="Anita Kapoor" />
            </div>
            <div class="input-field">
              <label for="register-email">Email</label>
              <input type="email" id="register-email" name="register-email" placeholder="chef@atelier.com" />
            </div>
            <div class="input-field">
              <label for="register-password">Password</label>
              <input type="password" id="register-password" name="register-password" placeholder="••••••••" />
            </div>
            <div class="input-field">
              <label for="register-confirm">Confirm password</label>
              <input type="password" id="register-confirm" name="register-confirm" placeholder="••••••••" />
            </div>
            <button class="button primary" type="submit">Create account</button>
          </form>
          <p class="auth-switch">
            Already a member? <a href="/online-sp/login.php">Sign in here</a>
          </p>
        </article>
        <article class="glass-panel auth-card">
          <span class="tag">Taste Club perks</span>
          <h2>Membership includes</h2>
          <ul class="form-stack">
            <li>Curated spice flights delivered monthly.</li>
            <li>Priority access to chef collaborations.</li>
            <li>Invitations to immersive tasting salons.</li>
          </ul>
          <a class="button outline" href="/online-sp/index.php#taste-club">Explore the Taste Club</a>
        </article>
      </section>
    </main>
<?php include __DIR__ . '/includes/footer.php'; ?>

