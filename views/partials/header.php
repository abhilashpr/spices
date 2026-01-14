<?php
/**
 * Header Partial
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../app/helpers/helpers.php';

// Get cart count
$cartCount = 0;
if (is_logged_in()) {
    require_once __DIR__ . '/../../app/models/CartModel.php';
    $cartModel = new CartModel();
    $user = get_logged_in_user();
    $userId = $user['id'] ?? null;
    if ($userId) {
        $cartCount = $cartModel->getCartCount($userId);
    }
} else {
    // Get count from session
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cartCount += (int)($item['quantity'] ?? 0);
        }
    }
}
?>
<div class="gradient-shell"></div>
<header class="glass-panel nav-bar">
  <a href="<?= url('index.php') ?>" class="brand">Wynvalley</a>
    <nav>
    <?php 
    // Get dynamic categories with subcategories
    $navbarCategories = get_navbar_categories();
    if (!empty($navbarCategories)): 
    ?>
    <div class="nav-item dropdown">
      <a href="<?= url('categories') ?>" class="nav-link">Categories</a>
      <div class="dropdown-menu">
        <?php foreach ($navbarCategories as $category): ?>
          <?php if (!empty($category['subcategories'])): ?>
            <div class="dropdown-column">
              <span class="dropdown-title"><?= e($category['name']) ?></span>
              <?php foreach ($category['subcategories'] as $subcategory): ?>
                <a href="<?= url('categories?filter=' . (int)$subcategory['id']) ?>">
                  <?= e($subcategory['name']) ?>
                </a>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="dropdown-column">
              <a href="<?= url('categories?filter=' . (int)$category['id']) ?>">
                <?= e($category['name']) ?>
              </a>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
    <a href="<?= url('index.php#best-sellers') ?>" class="nav-link">Best Sellers</a>
    <a href="<?= url('index.php#collections') ?>" class="nav-link">Collections</a>
    <a href="<?= url('index.php#craft') ?>" class="nav-link">Our Craft</a>
    <a href="<?= url('cart') ?>" class="nav-link cart-link">
      <span class="icon-cart" aria-hidden="true">
        <svg viewBox="0 0 24 24">
          <path
            d="M7 4h-2a1 1 0 0 0 0 2h2l1.6 7.59a3 3 0 0 0 2.95 2.41h6.09a1 1 0 0 0 0-2h-6.09a1 1 0 0 1-.98-.8L11.1 11h7.5a1 1 0 0 0 .98-.8l1-5a1 1 0 0 0-.98-1.2H7Zm12 18a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm-10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"
          />
        </svg>
        <?php if ($cartCount > 0): ?>
          <span class="cart-count-badge" id="cart-count-badge"><?= $cartCount ?></span>
        <?php endif; ?>
      </span>
      <span class="nav-text">Cart</span>
    </a>
    <?php if (is_logged_in()): ?>
      <?php $currentUser = get_logged_in_user(); ?>
      <div class="nav-item dropdown">
        <a href="<?= url('profile') ?>" class="button primary">My Profile</a>
        <div class="dropdown-menu" style="right: 0; left: auto;">
          <div class="dropdown-column">
            <span class="dropdown-title"><?= e($currentUser['name'] ?? 'User') ?></span>
            <a href="<?= url('profile') ?>">View Profile</a>
            <a href="<?= url('orders') ?>">My Orders</a>
            <a href="<?= url('wishlist') ?>">Wishlist</a>
            <a href="<?= url('addresses') ?>">Addresses</a>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid rgba(255,255,255,0.2);">
            <a href="<?= url('logout') ?>">Logout</a>
          </div>
        </div>
      </div>
    <?php else: ?>
      <a class="button primary" href="<?= url('login') ?>">Login</a>
    <?php endif; ?>
  </nav>
</header>

