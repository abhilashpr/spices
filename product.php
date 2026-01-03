<?php
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? null;
if (!$slug) {
    header('Location: /online-sp/categories.php');
    exit;
}

$product = get_product_by_slug($slug);
if (!$product) {
    header('HTTP/1.0 404 Not Found');
    $pageTitle = 'Product not found | Saffron & Spice';
} else {
    $pageTitle = $product['name'] . ' | Saffron & Spice';
}

$productMedia = [];
$relatedProducts = [];
$productReviews = [];
$descriptionParagraphs = [];
$shareUrl = '/online-sp/product.php?slug=' . urlencode($slug ?? '');

if ($product) {
    $productId = (int) $product['id'];
    $productMedia = get_product_media($productId);
    $relatedProducts = get_related_products($productId, 4);
    $productReviews = get_product_reviews($productId, 20);

    $fullDescription = $product['full_description'] ?? '';
    if (is_string($fullDescription) && trim($fullDescription) !== '') {
        $descriptionParagraphs = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $fullDescription))));
    }

    if (!empty($_SERVER['HTTP_HOST'])) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $shareUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . $shareUrl;
    }
}

$primaryMedia = $productMedia[0] ?? null;

include __DIR__ . '/includes/header.php';
?>
    <main class="product-main" id="product-main">
      <?php if ($product): ?>
        <section class="glass-panel product-hero" id="product-hero">
          <div class="product-gallery" aria-label="Product media gallery">
            <div class="product-media-primary">
              <?php if ($primaryMedia): ?>
                <?php if ($primaryMedia['media_type'] === 'video'): ?>
                  <video class="product-video" controls poster="<?= htmlspecialchars($product['image_class']) ?>">
                    <source src="<?= htmlspecialchars($primaryMedia['media_url']) ?>" type="video/mp4" />
                    Your browser does not support the video tag.
                  </video>
                <?php else: ?>
                  <img src="<?= htmlspecialchars($primaryMedia['media_url']) ?>" alt="<?= htmlspecialchars($primaryMedia['caption'] ?? $product['name']) ?>" />
                <?php endif; ?>
              <?php else: ?>
                <div class="card-image <?= htmlspecialchars($product['image_class']) ?>"></div>
              <?php endif; ?>
            </div>
            <?php if (count($productMedia) > 1): ?>
              <div class="product-media-thumbs" role="tablist">
                <?php foreach ($productMedia as $index => $media): ?>
                  <button
                    class="media-thumb<?= $index === 0 ? ' active' : '' ?>"
                    data-media-type="<?= htmlspecialchars($media['media_type']) ?>"
                    data-media-src="<?= htmlspecialchars($media['media_url']) ?>"
                    aria-label="View <?= htmlspecialchars($media['media_type']) ?>"
                    role="tab"
                  >
                    <?php if ($media['media_type'] === 'video'): ?>
                      <span class="thumb-icon">⏵</span>
                    <?php else: ?>
                      <img src="<?= htmlspecialchars($media['media_url']) ?>" alt="<?= htmlspecialchars($media['caption'] ?? $product['name']) ?> thumbnail" />
                    <?php endif; ?>
                  </button>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="product-info">
            <?php if (!empty($product['tag_line'])): ?>
              <span class="tag" id="product-tag"><?= htmlspecialchars($product['tag_line']) ?></span>
            <?php endif; ?>
            <h1 id="product-name"><?= htmlspecialchars($product['name']) ?></h1>
            <p id="product-description"><?= htmlspecialchars($product['summary']) ?></p>
            <div class="product-meta" id="product-meta">
              <span class="info-chip"><?= htmlspecialchars(str_replace('-', ' ', $product['region'])) ?></span>
              <span class="info-chip"><?= htmlspecialchars(str_replace('-', ' ', $product['craft'])) ?></span>
              <span class="info-chip">Heat: <?= htmlspecialchars(ucfirst($product['heat'])) ?></span>
            </div>
            <div class="product-actions">
              <div class="price-stack">
                <?php if (!empty($product['offer_price'])): ?>
                  <span class="price-regular" id="product-price-original"><?= format_price((float) $product['price']) ?></span>
                  <span class="price-accent" id="product-price"><?= format_price((float) $product['offer_price']) ?></span>
                <?php else: ?>
                  <span class="price-accent" id="product-price"><?= format_price((float) $product['price']) ?></span>
                <?php endif; ?>
              </div>
              <div class="weight-selector">
                <label class="weight-label">Select Weight</label>
                <div class="weight-badges">
                  <button type="button" class="weight-badge" data-weight="250g" aria-label="Select 250g">250g</button>
                  <button type="button" class="weight-badge" data-weight="350g" aria-label="Select 350g">350g</button>
                  <button type="button" class="weight-badge active" data-weight="500g" aria-label="Select 500g">500g</button>
                  <button type="button" class="weight-badge" data-weight="1kg" aria-label="Select 1kg">1kg</button>
                </div>
                <input type="hidden" id="selected-weight" name="weight" value="500g">
              </div>
              <div class="quantity-control">
                <label for="quantity">Qty</label>
                <div class="quantity-input-group">
                  <button type="button" class="quantity-btn quantity-decrease" aria-label="Decrease quantity">−</button>
                  <input type="number" id="quantity" name="quantity" value="1" min="1" max="6" readonly>
                  <button type="button" class="quantity-btn quantity-increase" aria-label="Increase quantity">+</button>
                </div>
              </div>
              <button class="button primary" type="button">Add to cart</button>
              <button class="button ghost" type="button" aria-label="Add to wishlist">
                <span aria-hidden="true">♡</span> Wishlist
              </button>
              <button class="button ghost" type="button" aria-label="Share product" data-share-url="<?= htmlspecialchars($shareUrl) ?>">
                <span aria-hidden="true">⇪</span> Share
              </button>
              <a class="button outline" href="/online-sp/categories.php">Back to categories</a>
            </div>
            <?php if (!empty($descriptionParagraphs)): ?>
              <div class="product-description-extended">
                <?php foreach ($descriptionParagraphs as $paragraph): ?>
                  <p><?= htmlspecialchars($paragraph) ?></p>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </section>

        <section class="glass-panel product-details-section" id="product-details">
          <div class="section-header">
            <span class="tag">Complete Information</span>
            <h2>Product Details</h2>
          </div>
          
          <div class="product-details-grid">
            <div class="details-specs">
              <div class="spec-item">
                <span class="spec-label">Weight</span>
                <span class="spec-value">250g, 350g, 500g, 1kg</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Packing</span>
                <span class="spec-value">Vacuum-sealed premium packaging</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Shelf Life</span>
                <span class="spec-value">24 months from date of manufacture</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Origin</span>
                <span class="spec-value"><?= htmlspecialchars(str_replace('-', ' ', ucwords($product['region']))) ?></span>
              </div>
            </div>

            <div class="details-content">
              <div class="detail-block">
                <h3 class="detail-title">Health Benefits</h3>
                <ul class="detail-list">
                  <li>Acting as a natural pain reliever due to the presence of capsaicin.</li>
                  <li>Boosting metabolism and aiding in weight loss when consumed in moderation.</li>
                  <li>Providing a good source of vitamins and minerals, including vitamin C and vitamin A.</li>
                  <li>Potentially having antibacterial and anti-inflammatory properties.</li>
                </ul>
              </div>

              <div class="detail-block">
                <h3 class="detail-title">How to Use</h3>
                <ul class="detail-list">
                  <li>Incorporate it into curries, stir-fries, and sauces to add intense heat and flavor.</li>
                  <li>Use it as a spicy garnish for soups, salads, and noodle dishes.</li>
                  <li>Create homemade hot sauces and marinades for a fiery kick.</li>
                </ul>
              </div>

              <div class="detail-block">
                <h3 class="detail-title">How to Store</h3>
                <ul class="detail-list">
                  <li>Store fresh bird's eye chilies in the refrigerator in a paper bag or perforated plastic bag for up to a week.</li>
                  <li>Dry bird's eye chilies can be stored in an airtight container in a cool, dry place, away from direct sunlight, for several months.</li>
                </ul>
              </div>

              <div class="detail-block">
                <h3 class="detail-title">Different Languages</h3>
                <div class="languages-grid">
                  <div class="language-item">
                    <span class="language-label">English:</span>
                    <span class="language-value">Bird's Eye Chilli</span>
                  </div>
                  <div class="language-item">
                    <span class="language-label">Malayalam:</span>
                    <span class="language-value">കാന്താരി മുളക് (Kanthari Mulaku)</span>
                  </div>
                  <div class="language-item">
                    <span class="language-label">Tamil:</span>
                    <span class="language-value">கொண்டத்தி மிளகு (Kondathi Milagu)</span>
                  </div>
                  <div class="language-item">
                    <span class="language-label">Kannada:</span>
                    <span class="language-value">ಹುಣಸೆ ಮೆಣಸು (Hunase Menasu)</span>
                  </div>
                  <div class="language-item">
                    <span class="language-label">Telugu:</span>
                    <span class="language-value">ఉరగాయాల మిరపకాయలు (Uragayala Mirapakayalu)</span>
                  </div>
                  <div class="language-item">
                    <span class="language-label">Hindi:</span>
                    <span class="language-value">बर्ड्स आई चिली (Bird's Eye Chilli)</span>
                  </div>
                  <div class="language-item">
                    <span class="language-label">Arabic:</span>
                    <span class="language-value">فلفل العين (Filfil al Ayn)</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section class="product-secondary" id="product-secondary">
          <?php $originNotes = split_notes($product['origin_notes']); ?>
          <?php $tastingNotes = split_notes($product['tasting_notes']); ?>
          <?php $usageNotes = split_notes($product['usage_notes']); ?>

          <?php if (!empty($originNotes)): ?>
            <article class="product-panel">
              <h2>Origins &amp; Provenance</h2>
              <ul>
                <?php foreach ($originNotes as $note): ?>
                  <li><?= htmlspecialchars($note) ?></li>
                <?php endforeach; ?>
              </ul>
            </article>
          <?php endif; ?>

          <?php if (!empty($tastingNotes)): ?>
            <article class="product-panel">
              <h2>Tasting Notes</h2>
              <ul>
                <?php foreach ($tastingNotes as $note): ?>
                  <li><?= htmlspecialchars($note) ?></li>
                <?php endforeach; ?>
              </ul>
            </article>
          <?php endif; ?>

          <?php if (!empty($usageNotes)): ?>
            <article class="product-panel">
              <h2>Chef's Pairings</h2>
              <ul>
                <?php foreach ($usageNotes as $note): ?>
                  <li><?= htmlspecialchars($note) ?></li>
                <?php endforeach; ?>
              </ul>
            </article>
          <?php endif; ?>
        </section>

        <?php if (!empty($relatedProducts)): ?>
          <section class="glass-panel related-products" id="related-products">
            <div class="related-header">
              <div>
                <span class="tag">Perfect Pairings</span>
                <h2>You may also love</h2>
              </div>
            </div>
            <div class="related-scroll-container">
              <div class="related-cards-wrapper">
                <?php foreach ($relatedProducts as $related): ?>
                  <article class="related-card" role="listitem">
                    <a href="/online-sp/product.php?slug=<?= urlencode($related['slug']) ?>" class="related-card-link">
                      <div class="related-card-image">
                        <div class="card-image <?= htmlspecialchars($related['image_class']) ?>"></div>
                        <div class="related-card-overlay">
                          <span class="view-product-btn">View →</span>
                        </div>
                      </div>
                      <div class="related-card-content">
                        <span class="related-tag"><?= htmlspecialchars($related['tag_line'] ?? 'Limited Edition') ?></span>
                        <h3 class="related-card-title"><?= htmlspecialchars($related['name']) ?></h3>
                        <div class="related-card-footer">
                          <span class="related-price"><?= format_price((float) $related['price']) ?></span>
                          <?php if (!empty($related['offer_price'])): ?>
                            <span class="related-price-offer"><?= format_price((float) $related['offer_price']) ?></span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </a>
                  </article>
                <?php endforeach; ?>
              </div>
            </div>
          </section>
        <?php endif; ?>

        <section class="glass-panel reviews-section" id="reviews">
          <div class="section-header">
            <span class="tag">Customer Feedback</span>
            <h2>Reviews &amp; Ratings</h2>
            <?php if (!empty($productReviews)): ?>
              <?php 
                $totalReviews = count($productReviews);
                $avgRating = array_sum(array_column($productReviews, 'rating')) / $totalReviews;
              ?>
              <div class="reviews-summary">
                <div class="rating-summary">
                  <span class="rating-large"><?= number_format($avgRating, 1) ?></span>
                  <div class="rating-stars-large">
                    <?php for ($star = 1; $star <= 5; $star++): ?>
                      <span class="star<?= $star <= round($avgRating) ? ' filled' : '' ?>">★</span>
                    <?php endfor; ?>
                  </div>
                  <span class="reviews-count">Based on <?= $totalReviews ?> review<?= $totalReviews !== 1 ? 's' : '' ?></span>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <?php if (!empty($productReviews)): ?>
            <div class="reviews-scroll-container">
              <div class="reviews-grid" role="list">
                <?php foreach ($productReviews as $review): ?>
                  <article class="review-card" role="listitem">
                    <header class="review-header">
                      <div class="reviewer-info">
                        <div class="reviewer-avatar">
                          <?= strtoupper(substr($review['reviewer_name'], 0, 1)) ?>
                        </div>
                        <div class="reviewer-details">
                          <span class="reviewer-name"><?= htmlspecialchars($review['reviewer_name']) ?></span>
                          <time class="review-date" datetime="<?= htmlspecialchars($review['created_at']) ?>">
                            <?= htmlspecialchars(date('F j, Y', strtotime($review['created_at']))) ?>
                          </time>
                        </div>
                      </div>
                      <div class="rating" aria-label="Rating <?= (int) $review['rating'] ?> out of 5">
                        <?php for ($star = 1; $star <= 5; $star++): ?>
                          <span class="star<?= $star <= (int) $review['rating'] ? ' filled' : '' ?>" aria-hidden="true">★</span>
                        <?php endfor; ?>
                      </div>
                    </header>
                    <?php if (!empty($review['headline'])): ?>
                      <h3 class="review-headline"><?= htmlspecialchars($review['headline']) ?></h3>
                    <?php endif; ?>
                    <p class="review-body"><?= htmlspecialchars($review['review_text']) ?></p>
                  </article>
                <?php endforeach; ?>
              </div>
            </div>
          <?php else: ?>
            <div class="empty-state subtle" role="status">
              <div class="empty-icon">⭐</div>
              <h3>Be the first to review this product</h3>
              <p>Share your experience and help others discover this amazing spice blend.</p>
              <button class="button primary" type="button">Write a review</button>
            </div>
          <?php endif; ?>
        </section>
      <?php else: ?>
        <section class="glass-panel empty-state" id="product-empty">
          <h3>We couldn't find that blend.</h3>
          <p>
            The spice you're looking for may have sold out or been moved. Explore our curated collections to discover similar
            profiles.
          </p>
          <a class="button primary" href="/online-sp/categories.php">Browse categories</a>
        </section>
      <?php endif; ?>
    </main>
    <script>
      // Quantity control functionality
      (function() {
        const quantityInput = document.getElementById('quantity');
        const decreaseBtn = document.querySelector('.quantity-decrease');
        const increaseBtn = document.querySelector('.quantity-increase');
        
        if (!quantityInput || !decreaseBtn || !increaseBtn) return;
        
        const min = parseInt(quantityInput.getAttribute('min')) || 1;
        const max = parseInt(quantityInput.getAttribute('max')) || 6;
        
        function updateButtons() {
          const value = parseInt(quantityInput.value) || min;
          decreaseBtn.disabled = value <= min;
          increaseBtn.disabled = value >= max;
        }
        
        decreaseBtn.addEventListener('click', function() {
          const currentValue = parseInt(quantityInput.value) || min;
          if (currentValue > min) {
            quantityInput.value = currentValue - 1;
            updateButtons();
          }
        });
        
        increaseBtn.addEventListener('click', function() {
          const currentValue = parseInt(quantityInput.value) || min;
          if (currentValue < max) {
            quantityInput.value = currentValue + 1;
            updateButtons();
          }
        });
        
        // Initialize button states
        updateButtons();
      })();

      // Weight badge selection functionality
      (function() {
        const weightBadges = document.querySelectorAll('.weight-badge');
        const hiddenInput = document.getElementById('selected-weight');
        
        if (!weightBadges.length || !hiddenInput) return;
        
        weightBadges.forEach(function(badge) {
          badge.addEventListener('click', function() {
            // Remove active class from all badges
            weightBadges.forEach(function(b) {
              b.classList.remove('active');
            });
            
            // Add active class to clicked badge
            this.classList.add('active');
            
            // Update hidden input value
            const weight = this.getAttribute('data-weight');
            if (hiddenInput) {
              hiddenInput.value = weight;
            }
          });
        });
      })();

      // Reviews scroll container functionality
      (function() {
        const scrollContainer = document.querySelector('.reviews-scroll-container');
        if (!scrollContainer) return;
        
        function checkScrollable() {
          const hasScroll = scrollContainer.scrollHeight > scrollContainer.clientHeight;
          if (hasScroll) {
            scrollContainer.classList.add('has-scroll');
          } else {
            scrollContainer.classList.remove('has-scroll');
          }
        }
        
        // Check on load
        checkScrollable();
        
        // Check on scroll
        scrollContainer.addEventListener('scroll', function() {
          const isAtBottom = scrollContainer.scrollHeight - scrollContainer.scrollTop <= scrollContainer.clientHeight + 10;
          if (isAtBottom) {
            scrollContainer.classList.remove('has-scroll');
          } else {
            scrollContainer.classList.add('has-scroll');
          }
        });
        
        // Check on resize
        window.addEventListener('resize', checkScrollable);
      })();
    </script>
<?php include __DIR__ . '/includes/footer.php'; ?>

