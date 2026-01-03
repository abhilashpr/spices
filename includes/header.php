<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $pageTitle ?? 'Saffron & Spice | Premium Spice Boutique'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Poppins:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="/online-sp/styles.css" />
  </head>
  <body>
    <div class="gradient-shell"></div>
    <header class="glass-panel nav-bar">
      <a href="/online-sp/index.php" class="brand">Saffron &amp; Spice</a>
      <nav>
        <div class="nav-item dropdown">
          <a href="/online-sp/categories.php" class="nav-link">Categories</a>
          <div class="dropdown-menu">
            <div class="dropdown-column">
              <span class="dropdown-title">By Region</span>
              <a href="/online-sp/categories.php?filter=middle-east">Levant &amp; Maghreb</a>
              <a href="/online-sp/categories.php?filter=south-asia">South Asian Heritage</a>
              <a href="/online-sp/categories.php?filter=silk-road">Silk Road Classics</a>
            </div>
            <div class="dropdown-column">
              <span class="dropdown-title">By Craft</span>
              <a href="/online-sp/categories.php?filter=smoked">Smoked &amp; Fired</a>
              <a href="/online-sp/categories.php?filter=herbal">Botanical Infusions</a>
              <a href="/online-sp/categories.php?filter=heat">Heat &amp; Ember</a>
            </div>
          </div>
        </div>
        <a href="/online-sp/index.php#best-sellers" class="nav-link">Best Sellers</a>
        <a href="/online-sp/index.php#collections" class="nav-link">Collections</a>
        <a href="/online-sp/index.php#craft" class="nav-link">Our Craft</a>
        <a href="/online-sp/login.php" class="nav-link">Login</a>
        <a href="/online-sp/register.php" class="nav-link">Register</a>
        <a href="/online-sp/cart.php" class="nav-link cart-link">
          <span class="icon-cart" aria-hidden="true">
            <svg viewBox="0 0 24 24">
              <path
                d="M7 4h-2a1 1 0 0 0 0 2h2l1.6 7.59a3 3 0 0 0 2.95 2.41h6.09a1 1 0 0 0 0-2h-6.09a1 1 0 0 1-.98-.8L11.1 11h7.5a1 1 0 0 0 .98-.8l1-5a1 1 0 0 0-.98-1.2H7Zm12 18a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm-10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"
              />
            </svg>
          </span>
          <span class="nav-text">Cart</span>
        </a>
        <a class="button primary" href="/online-sp/index.php#shop">Shop Now</a>
      </nav>
    </header>
