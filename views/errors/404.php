<?php
/**
 * 404 Error View
 */
require_once __DIR__ . '/../../app/helpers/helpers.php';
?>
<section class="error-page-main">
  <div class="error-container">
    <div class="glass-panel error-panel">
      <h1>404</h1>
      <h2>Page Not Found</h2>
      <p>The page you're looking for doesn't exist.</p>
      <a href="<?= url('index.php') ?>" class="button primary">Go Home</a>
    </div>
  </div>
</section>

