<?php
/**
 * Product Detail View
 */
require_once __DIR__ . '/../../app/helpers/helpers.php';

// Helper function if not already loaded
if (!function_exists('split_notes')) {
    function split_notes(?string $notes): array {
        if ($notes === null || trim($notes) === '') {
            return [];
        }
        $lines = preg_split('/\r?\n/', trim($notes));
        return array_values(array_filter(array_map('trim', $lines)));
    }
}
?>
<?php if ($product): ?>
  <main class="product-main" id="product-main">
    <section class="glass-panel product-hero" id="product-hero">
      <div class="product-gallery" aria-label="Product media gallery">
        <div class="product-media-primary">
          <!-- Icon Buttons Container -->
          <div class="product-image-actions">
            <!-- Wishlist Icon Button -->
            <button type="button" class="image-action-btn wishlist-icon-btn<?= ($isInWishlist ?? false) ? ' added' : '' ?>" aria-label="Add to wishlist" data-product-id="<?= e($product['id']) ?>">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
              </svg>
            </button>
            <!-- Share Icon Button -->
            <button type="button" class="image-action-btn share-icon-btn" aria-label="Share product" data-share-url="<?= e($shareUrl) ?>">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="18" cy="5" r="3"></circle>
                <circle cx="6" cy="12" r="3"></circle>
                <circle cx="18" cy="19" r="3"></circle>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
              </svg>
            </button>
          </div>
          <?php if ($mainImage): ?>
            <img id="product-main-image" src="<?= e($mainImage) ?>" alt="<?= e($product['name']) ?>" />
          <?php elseif (!empty($productImages) && isset($productImages[0])): ?>
            <?php $firstImg = get_image_url($productImages[0]['image_path']); ?>
            <img id="product-main-image" src="<?= e($firstImg) ?>" alt="<?= e($product['name']) ?>" />
          <?php elseif (!empty($productMedia) && isset($productMedia[0])): ?>
            <?php $primaryMedia = $productMedia[0]; ?>
            <?php if ($primaryMedia['media_type'] === 'video'): ?>
              <video id="product-main-media" class="product-video" controls poster="<?= e($product['image_class'] ?? '') ?>">
                <source src="<?= e($primaryMedia['media_url']) ?>" type="video/mp4" />
                Your browser does not support the video tag.
              </video>
            <?php else: ?>
              <img id="product-main-image" src="<?= e($primaryMedia['media_url']) ?>" alt="<?= e($primaryMedia['caption'] ?? $product['name']) ?>" />
            <?php endif; ?>
          <?php else: ?>
            <div class="card-image <?= e($product['image_class'] ?? '') ?>"></div>
          <?php endif; ?>
        </div>
        <?php 
          // Combine main image with product images for thumbnail list
          $allImages = [];
          if ($mainImage) {
            $allImages[] = ['image_path' => $product['main_image'], 'is_main' => true];
          }
          if (!empty($productImages)) {
            foreach ($productImages as $img) {
              // Skip main image if it's already added
              if (!$mainImage || $img['image_path'] !== $product['main_image']) {
                $allImages[] = $img;
              }
            }
          }
        ?>
        <?php if (!empty($allImages) && count($allImages) > 1): ?>
          <div class="product-media-thumbs" role="tablist">
            <?php foreach ($allImages as $index => $img): ?>
              <?php $imgUrl = isset($img['is_main']) ? $mainImage : get_image_url($img['image_path']); ?>
              <button
                class="media-thumb<?= $index === 0 ? ' active' : '' ?>"
                data-media-type="image"
                data-media-src="<?= e($imgUrl) ?>"
                aria-label="View image <?= $index + 1 ?>"
                role="tab"
              >
                <img src="<?= e($imgUrl) ?>" alt="Thumbnail <?= $index + 1 ?>" />
              </button>
            <?php endforeach; ?>
          </div>
        <?php elseif (!empty($productMedia) && count($productMedia) > 1): ?>
          <div class="product-media-thumbs" role="tablist">
            <?php 
              // Add main image to the start of productMedia if it exists and is not already in the list
              $allMedia = [];
              if ($mainImage && !empty($productMedia)) {
                $mainInMedia = false;
                foreach ($productMedia as $media) {
                  if ($media['media_type'] === 'image' && $media['media_url'] === $mainImage) {
                    $mainInMedia = true;
                    break;
                  }
                }
                if (!$mainInMedia) {
                  $allMedia[] = ['media_type' => 'image', 'media_url' => $mainImage, 'caption' => $product['name'], 'is_main' => true];
                }
              }
              foreach ($productMedia as $media) {
                $allMedia[] = $media;
              }
              $displayMedia = !empty($allMedia) ? $allMedia : $productMedia;
            ?>
            <?php foreach ($displayMedia as $index => $media): ?>
              <button
                class="media-thumb<?= $index === 0 ? ' active' : '' ?>"
                data-media-type="<?= e($media['media_type']) ?>"
                data-media-src="<?= e($media['media_url']) ?>"
                aria-label="View <?= e($media['media_type']) ?>"
                role="tab"
              >
                <?php if ($media['media_type'] === 'video'): ?>
                  <span class="thumb-icon">⏵</span>
                <?php else: ?>
                  <img src="<?= e($media['media_url']) ?>" alt="<?= e($media['caption'] ?? $product['name']) ?> thumbnail" />
                <?php endif; ?>
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="product-info">
        <?php if (!empty($product['tag_line'])): ?>
          <span class="tag" id="product-tag"><?= e($product['tag_line']) ?></span>
        <?php endif; ?>
        <h1 id="product-name"><?= e($product['name']) ?></h1>
        
        <!-- Price under title -->
        <div class="price-stack">
          <?php 
            // Get price from first SKU if available, otherwise use product price
            $displayPrice = !empty($productSKUs) && isset($productSKUs[0]) ? $productSKUs[0]['price'] : ($product['price'] ?? 0);
            $displayOfferPrice = !empty($productSKUs) && isset($productSKUs[0]) ? ($productSKUs[0]['offer_price'] ?? null) : ($product['offer_price'] ?? null);
            $hasOffer = $displayOfferPrice && $displayOfferPrice < $displayPrice;
            $discountPercent = 0;
            if ($hasOffer && $displayPrice > 0) {
              $discountPercent = round((($displayPrice - $displayOfferPrice) / $displayPrice) * 100);
            }
          ?>
          <?php if ($hasOffer): ?>
            <span class="price-regular" id="product-price-original"><?= format_price((float) $displayPrice) ?></span>
            <span class="price-accent" id="product-price"><?= format_price((float) $displayOfferPrice) ?></span>
            <?php if ($discountPercent > 0): ?>
              <span class="discount-badge-animated"><?= $discountPercent ?>% OFF</span>
            <?php endif; ?>
          <?php else: ?>
            <span class="price-accent" id="product-price"><?= format_price((float) $displayPrice) ?></span>
          <?php endif; ?>
        </div>

        <!-- Availability Badge -->
        <div class="availability-badge-container">
          <?php $isOutOfStock = isset($product['is_out_of_stock']) && $product['is_out_of_stock']; ?>
          <span class="availability-badge <?= $isOutOfStock ? 'out-of-stock' : 'in-stock' ?>">
            <?= $isOutOfStock ? 'Out of Stock' : 'In Stock' ?>
          </span>
        </div>

        <!-- Rating -->
        <?php 
        if (!empty($productReviews)):
          $reviewCount = count($productReviews);
          $avgRating = array_sum(array_column($productReviews, 'rating')) / $reviewCount;
          $rating = round($avgRating, 1);
          $roundedRating = round($avgRating);
        ?>
          <div class="product-rating-header">
            <div class="rating-stars-small">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <span class="star <?= $i <= $roundedRating ? 'filled' : '' ?>"><?= $i <= $roundedRating ? '★' : '☆' ?></span>
              <?php endfor; ?>
            </div>
            <span class="rating-value"><?= number_format($rating, 1) ?></span>
            <span class="rating-count">(<?= $reviewCount ?> review<?= $reviewCount !== 1 ? 's' : '' ?>)</span>
          </div>
        <?php endif; ?>

        <!-- Description (smaller font, max 250 chars) -->
        <?php 
          $fullDescription = $product['summary'] ?? $product['description'] ?? '';
          $descriptionLength = mb_strlen($fullDescription);
          $description = mb_substr($fullDescription, 0, 250);
          if ($descriptionLength > 250) {
            $description .= '...';
          }
        ?>
        <p id="product-description" class="product-description-small"><?= e($description) ?></p>

        <!-- Product Code and SKU Code (same line) -->
        <div class="product-meta-info">
          <div class="meta-item meta-item-inline">
            <?php if (!empty($product['product_code'])): ?>
              <span class="meta-label">Product Code:</span>
              <span class="meta-value"><?= e($product['product_code']) ?></span>
            <?php endif; ?>
            <?php 
            // Show SKU code if available (check first SKU or use SKU ID)
            if (!empty($productSKUs)): 
              $firstSku = $productSKUs[0];
              $skuCode = $firstSku['sku_code'] ?? $firstSku['code'] ?? (!empty($product['product_code']) ? $product['product_code'] . '-SKU' . $firstSku['id'] : 'SKU-' . $firstSku['id']);
            ?>
              <span class="meta-separator">|</span>
              <span class="meta-label">SKU Code:</span>
              <span class="meta-value"><?= e($skuCode) ?></span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Quantity Selection and SKU Selector -->
        <div class="product-selection-group">
          <?php if (!empty($productSKUs)): ?>
            <div class="weight-selector">
              <label class="weight-label">Select Weight</label>
              <div class="weight-badges">
                <?php foreach ($productSKUs as $index => $sku): ?>
                  <?php 
                    $unitName = $sku['unit_name'] ?? '';
                    $unitSymbol = $sku['unit_symbol'] ?? '';
                    $skuValue = $sku['value'] ?? '';
                    $displayText = trim($skuValue . ' ' . $unitSymbol);
                    $skuPrice = $sku['price'] ?? 0;
                    $skuOfferPrice = $sku['offer_price'] ?? null;
                    $finalPrice = $skuOfferPrice && $skuOfferPrice < $skuPrice ? $skuOfferPrice : $skuPrice;
                  ?>
                  <button type="button" class="weight-badge <?= $index === 0 ? 'active' : '' ?>" 
                          data-sku-id="<?= $sku['id'] ?>" 
                          data-weight="<?= e($displayText) ?>"
                          data-price="<?= $finalPrice ?>"
                          data-original-price="<?= $skuPrice ?>"
                          data-offer-price="<?= $skuOfferPrice ?>"
                          aria-label="Select <?= e($displayText) ?>">
                    <?= e($displayText) ?>
                  </button>
                <?php endforeach; ?>
              </div>
              <input type="hidden" id="selected-sku-id" name="sku_id" value="<?= $productSKUs[0]['id'] ?? '' ?>">
            </div>
          <?php else: ?>
            <div class="weight-selector">
              <label class="weight-label">Select Weight</label>
              <div class="weight-badges">
                <button type="button" class="weight-badge active" data-weight="250g" aria-label="Select 250g">250g</button>
                <button type="button" class="weight-badge" data-weight="350g" aria-label="Select 350g">350g</button>
                <button type="button" class="weight-badge" data-weight="500g" aria-label="Select 500g">500g</button>
                <button type="button" class="weight-badge" data-weight="1kg" aria-label="Select 1kg">1kg</button>
              </div>
              <input type="hidden" id="selected-weight" name="weight" value="500g">
            </div>
          <?php endif; ?>
          
          <div class="quantity-control">
            <label for="quantity">Qty</label>
            <div class="quantity-input-group">
              <button type="button" class="quantity-btn quantity-decrease" aria-label="Decrease quantity">−</button>
              <input type="number" id="quantity" name="quantity" value="1" min="1" max="6" readonly>
              <button type="button" class="quantity-btn quantity-increase" aria-label="Increase quantity">+</button>
            </div>
          </div>
        </div>

        <div class="product-actions">
          <button class="button primary add-to-cart-large" type="button" id="add-to-cart-btn">Add to cart</button>
        </div>
        <?php if (!empty($descriptionParagraphs)): ?>
          <div class="product-description-extended">
            <?php foreach ($descriptionParagraphs as $paragraph): ?>
              <p><?= e($paragraph) ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="glass-panel product-details-section" id="product-details">
      <div class="section-header">
        <h2>Product Details</h2>
      </div>
      
      <!-- Description with See More -->
      <?php 
        $fullDescription = strip_tags($product['summary'] ?? $product['description'] ?? '');
        $descriptionLength = mb_strlen($fullDescription);
        $truncatedDescription = mb_substr($fullDescription, 0, 200);
      ?>
      <?php if (!empty($fullDescription)): ?>
        <div class="product-full-description-wrapper">
          <div class="product-full-description" id="product-full-description">
            <p><?= nl2br(e($truncatedDescription)) ?><?php if ($descriptionLength > 200): ?><span class="description-more-text" style="display: none;"><?= nl2br(e(mb_substr($fullDescription, 200))) ?></span><?php endif; ?></p>
          </div>
          <?php if ($descriptionLength > 200): ?>
            <button type="button" class="btn-see-more" id="btn-see-more" onclick="toggleDescription()">
              <span class="see-more-text">See more</span>
              <span class="see-less-text" style="display: none;">See less</span>
            </button>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      
      <div class="product-details-grid">
        <div class="details-specs">
          <?php if (!empty($product['region'])): ?>
            <div class="spec-item">
              <span class="spec-label">Origin</span>
              <span class="spec-value"><?= e(ucwords(str_replace('-', ' ', $product['region']))) ?></span>
            </div>
          <?php endif; ?>
          <div class="spec-item">
            <span class="spec-label">Weight</span>
            <span class="spec-value">
              <?php if (!empty($productSKUs)): ?>
                <?= e(implode(', ', array_map(function($sku) {
                  return trim(($sku['value'] ?? '') . ' ' . ($sku['unit_symbol'] ?? ''));
                }, $productSKUs))) ?>
              <?php else: ?>
                250g, 350g, 500g, 1kg
              <?php endif; ?>
            </span>
          </div>
          <div class="spec-item">
            <span class="spec-label">Packing</span>
            <span class="spec-value">Vacuum-sealed premium packaging</span>
          </div>
          <div class="spec-item">
            <span class="spec-label">Shelf Life</span>
            <span class="spec-value">24 months from date of manufacture</span>
          </div>
          
          <?php if (!empty($productLanguages)): ?>
            <div class="spec-item spec-item-languages">
              <span class="spec-label">Different Languages</span>
              <div class="languages-list">
                <?php foreach ($productLanguages as $lang): ?>
                  <div class="language-item">
                    <span class="language-code"><?= strtoupper(e($lang['language_code'])) ?></span>
                    <span class="language-name"><?= e($lang['product_name']) ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <div class="details-content">
          <?php
            // Get benefits from database if available
            $healthBenefits = [];
            $howToUse = [];
            $howToStore = [];
            
            if (isset($benefitsByType)) {
              $healthBenefits = $benefitsByType['health_benefits'] ?? [];
              $howToUse = $benefitsByType['how_to_use'] ?? [];
              $howToStore = $benefitsByType['how_to_store'] ?? [];
            }
          ?>
          
          <?php if (!empty($healthBenefits)): ?>
            <div class="detail-block">
              <h3 class="detail-title">Health Benefits</h3>
              <ul class="detail-list">
                <?php foreach ($healthBenefits as $benefit): ?>
                  <li><?= e($benefit['benefit_text']) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if (!empty($howToUse)): ?>
            <div class="detail-block">
              <h3 class="detail-title">How to Use</h3>
              <ul class="detail-list">
                <?php foreach ($howToUse as $benefit): ?>
                  <li><?= e($benefit['benefit_text']) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if (!empty($howToStore)): ?>
            <div class="detail-block">
              <h3 class="detail-title">How to Store</h3>
              <ul class="detail-list">
                <?php foreach ($howToStore as $benefit): ?>
                  <li><?= e($benefit['benefit_text']) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <?php 
      $originNotes = split_notes($product['origin_notes'] ?? null);
      $tastingNotes = split_notes($product['tasting_notes'] ?? null);
      $usageNotes = split_notes($product['usage_notes'] ?? null);
    ?>
    
    <?php if (!empty($originNotes) || !empty($tastingNotes) || !empty($usageNotes)): ?>
      <section class="product-secondary" id="product-secondary">
        <?php if (!empty($originNotes)): ?>
          <article class="product-panel">
            <h2>Origins &amp; Provenance</h2>
            <ul>
              <?php foreach ($originNotes as $note): ?>
                <li><?= e($note) ?></li>
              <?php endforeach; ?>
            </ul>
          </article>
        <?php endif; ?>

        <?php if (!empty($tastingNotes)): ?>
          <article class="product-panel">
            <h2>Tasting Notes</h2>
            <ul>
              <?php foreach ($tastingNotes as $note): ?>
                <li><?= e($note) ?></li>
              <?php endforeach; ?>
            </ul>
          </article>
        <?php endif; ?>

        <?php if (!empty($usageNotes)): ?>
          <article class="product-panel">
            <h2>Chef's Pairings</h2>
            <ul>
              <?php foreach ($usageNotes as $note): ?>
                <li><?= e($note) ?></li>
              <?php endforeach; ?>
            </ul>
          </article>
        <?php endif; ?>
      </section>
    <?php endif; ?>

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
                <a href="<?= url('product.php?slug=' . urlencode($related['slug'])) ?>" class="related-card-link">
                  <div class="related-card-image">
                    <?php if (!empty($related['main_image'])): ?>
                      <?php $relatedImgUrl = get_image_url($related['main_image']); ?>
                      <img src="<?= e($relatedImgUrl) ?>" alt="<?= e($related['name']) ?>" />
                    <?php else: ?>
                      <div class="card-image <?= e($related['image_class'] ?? '') ?>"></div>
                    <?php endif; ?>
                    <div class="related-card-overlay">
                      <span class="view-product-btn">View →</span>
                    </div>
                  </div>
                  <div class="related-card-content">
                    <span class="related-tag"><?= e($related['tag_line'] ?? 'Limited Edition') ?></span>
                    <h3 class="related-card-title"><?= e($related['name']) ?></h3>
                    <div class="related-card-footer">
                      <?php if (!empty($related['offer_price']) && $related['offer_price'] < $related['price']): ?>
                        <span class="related-price-offer"><?= format_price((float) $related['offer_price']) ?></span>
                        <span class="related-price" style="text-decoration: line-through; opacity: 0.6;"><?= format_price((float) $related['price']) ?></span>
                      <?php else: ?>
                        <span class="related-price"><?= format_price((float) ($related['price'] ?? 0)) ?></span>
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

    <?php if (!empty($productReviews)): ?>
      <section class="glass-panel reviews-section" id="reviews">
        <div class="section-header">
          <h2>Reviews &amp; Ratings</h2>
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
        </div>
        <div class="reviews-scroll-container">
          <div class="reviews-grid" role="list">
            <?php foreach ($productReviews as $review): ?>
              <article class="review-card" role="listitem">
                <header class="review-header">
                  <div class="reviewer-info">
                    <div class="reviewer-avatar">
                      <?= strtoupper(substr($review['reviewer_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="reviewer-details">
                      <span class="reviewer-name"><?= e($review['reviewer_name'] ?? 'Anonymous') ?></span>
                      <time class="review-date" datetime="<?= e($review['created_at'] ?? '') ?>">
                        <?= !empty($review['created_at']) ? date('F j, Y', strtotime($review['created_at'])) : '' ?>
                      </time>
                    </div>
                  </div>
                  <div class="rating" aria-label="Rating <?= (int) ($review['rating'] ?? 0) ?> out of 5">
                    <?php for ($star = 1; $star <= 5; $star++): ?>
                      <span class="star<?= $star <= (int) ($review['rating'] ?? 0) ? ' filled' : '' ?>" aria-hidden="true">★</span>
                    <?php endfor; ?>
                  </div>
                </header>
                <?php if (!empty($review['headline'])): ?>
                  <h3 class="review-headline"><?= e($review['headline']) ?></h3>
                <?php endif; ?>
                <p class="review-body"><?= e($review['review_text'] ?? '') ?></p>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php else: ?>
      <section class="glass-panel reviews-section" id="reviews">
        <div class="section-header">
          <h2>Reviews &amp; Ratings</h2>
        </div>
        <div class="empty-state subtle" role="status">
          <div class="empty-icon">⭐</div>
          <h3>Be the first to review this product</h3>
          <p>Share your experience and help others discover this amazing spice blend.</p>
          <button class="button primary" type="button">Write a review</button>
        </div>
      </section>
    <?php endif; ?>
  </main>
<?php else: ?>
  <main class="product-main" id="product-main">
    <section class="glass-panel empty-state" id="product-empty">
      <h3>We couldn't find that blend.</h3>
      <p>
        The spice you're looking for may have sold out or been moved. Explore our curated collections to discover similar
        profiles.
      </p>
      <a class="button primary" href="<?= url('categories.php') ?>">Browse categories</a>
    </section>
  </main>
<?php endif; ?>

<script src="<?= url('assets/js/product.js') ?>"></script>
<script>
// Show message notification at bottom right corner (global function)
function showToastMessage(message, type = 'success') {
  console.log('showToastMessage called:', message, type); // Debug log
  
  // Remove existing message if any
  const existingMessage = document.querySelector('.cart-message');
  if (existingMessage) {
    existingMessage.remove();
  }
  
  // Create message element
  const messageEl = document.createElement('div');
  messageEl.className = 'cart-message' + (type === 'error' ? ' error' : ' success');
  messageEl.textContent = message;
  document.body.appendChild(messageEl);
  
  console.log('Message element created:', messageEl); // Debug log
  
  // Force reflow and show message immediately
  void messageEl.offsetHeight; // Force reflow
  setTimeout(() => {
    messageEl.classList.add('show');
    console.log('Show class added'); // Debug log
  }, 10);
  
  // Auto-hide after 3.5 seconds
  setTimeout(() => {
    messageEl.classList.remove('show');
    setTimeout(() => {
      if (messageEl.parentNode) {
        messageEl.parentNode.removeChild(messageEl);
      }
    }, 400);
  }, 3500);
}

// Product Image Actions Functionality
(function() {

  // Wishlist Icon Button
  const wishlistBtn = document.querySelector('.wishlist-icon-btn');
  if (wishlistBtn) {
    wishlistBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const productId = this.dataset.productId;
      const btn = this;
      
      // Toggle wishlist via API
      fetch('/online-sp/wishlist?action=toggle', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ product_id: productId })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        console.log('Wishlist response:', data);
        if (data.login_required) {
          // Show login required toast
          showToastMessage(data.message, 'error');
        } else if (data.success) {
          // Toggle added state
          if (data.in_wishlist) {
            btn.classList.add('added');
            btn.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>';
          } else {
            btn.classList.remove('added');
            btn.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>';
          }
        } else {
          showToastMessage(data.message || 'Failed to update wishlist', 'error');
        }
      })
      .catch(error => {
        console.error('Wishlist error:', error);
        showToastMessage('Failed to update wishlist. Please try again.', 'error');
      });
    });
  }
  
  // Share Icon Button - includes SKU in URL
  const shareBtn = document.querySelector('.share-icon-btn');
  if (shareBtn) {
    shareBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      // Get current SKU from selected SKU ID
      const selectedSkuId = document.getElementById('selected-sku-id');
      const skuId = selectedSkuId ? selectedSkuId.value : '';
      
      // Use current full URL and update SKU parameter if needed
      const currentUrl = window.location.href;
      const url = new URL(currentUrl);
      
      // Update SKU parameter if SKU is selected
      if (skuId) {
        url.searchParams.set('sku', skuId);
      }
      
      const shareUrl = url.toString();
      
      console.log('Copying URL:', shareUrl); // Debug log
      
      // Copy URL to clipboard
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(shareUrl).then(() => {
          // Show flash message
          showCopyFlashMessage();
        }).catch(err => {
          console.error('Failed to copy:', err);
          // Fallback for older browsers
          copyToClipboardFallback(shareUrl);
        });
      } else {
        // Fallback for older browsers
        copyToClipboardFallback(shareUrl);
      }
    });
  }
  
  // Fallback function for older browsers
  function copyToClipboardFallback(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
      const successful = document.execCommand('copy');
      if (successful) {
        showCopyFlashMessage();
      } else {
        alert('Failed to copy link. Please copy manually: ' + text);
      }
    } catch (err) {
      console.error('Fallback copy failed:', err);
      alert('Failed to copy link. Please copy manually: ' + text);
    } finally {
      document.body.removeChild(textArea);
    }
  }

  // Show flash message notification
  function showCopyFlashMessage() {
    // Remove existing flash message if any
    const existingFlash = document.getElementById('copy-flash-message');
    if (existingFlash) {
      existingFlash.remove();
    }
    
    // Find the product hero section to append the message
    const productHero = document.querySelector('.product-hero') || document.querySelector('.product-gallery') || document.querySelector('.product-media-primary');
    if (!productHero) {
      // Fallback to body if product section not found
      return;
    }
    
    // Create flash message element
    const flashMessage = document.createElement('div');
    flashMessage.id = 'copy-flash-message';
    flashMessage.className = 'copy-flash-message';
    flashMessage.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"></path></svg><span>Copied!</span>';
    
    // Append to product hero section
    productHero.appendChild(flashMessage);
    
    // Trigger animation
    setTimeout(() => {
      flashMessage.classList.add('show');
    }, 10);
    
    // Remove after animation
    setTimeout(() => {
      flashMessage.classList.remove('show');
      setTimeout(() => {
        if (flashMessage.parentNode) {
          flashMessage.remove();
        }
      }, 300);
    }, 2000);
  }
})();

// Show message notification at bottom right corner (global function)
function showToastMessage(message, type = 'success') {
  console.log('showToastMessage called:', message, type); // Debug log
  
  // Remove existing message if any
  const existingMessage = document.querySelector('.cart-message');
  if (existingMessage) {
    existingMessage.remove();
  }
  
  // Create message element
  const messageEl = document.createElement('div');
  messageEl.className = 'cart-message' + (type === 'error' ? ' error' : ' success');
  messageEl.textContent = message;
  document.body.appendChild(messageEl);
  
  console.log('Message element created:', messageEl); // Debug log
  
  // Force reflow and show message immediately
  void messageEl.offsetHeight; // Force reflow
  setTimeout(() => {
    messageEl.classList.add('show');
    console.log('Show class added'); // Debug log
  }, 10);
  
  // Auto-hide after 3.5 seconds
  setTimeout(() => {
    messageEl.classList.remove('show');
    setTimeout(() => {
      if (messageEl.parentNode) {
        messageEl.parentNode.removeChild(messageEl);
      }
    }, 400);
  }, 3500);
}

// Add to Cart Functionality
(function() {
  const addToCartBtn = document.getElementById('add-to-cart-btn');
  if (!addToCartBtn) {
    console.log('Add to cart button not found');
    return;
  }

  addToCartBtn.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();

    // Get product ID
    const productId = <?= $product['id'] ?? 0 ?>;
    if (!productId) {
      showToastMessage('Product ID not found', 'error');
      return;
    }

    // Get selected SKU ID
    const selectedSkuIdEl = document.getElementById('selected-sku-id');
    const skuId = selectedSkuIdEl ? parseInt(selectedSkuIdEl.value) : 0;
    if (!skuId) {
      showToastMessage('Please select a weight/unit', 'error');
      return;
    }

    // Get quantity
    const quantityInput = document.getElementById('quantity');
    const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
    if (quantity < 1) {
      showToastMessage('Invalid quantity', 'error');
      return;
    }

    // Get unit from selected SKU badge
    const selectedBadge = document.querySelector('.weight-badge.active');
    const unit = selectedBadge ? (selectedBadge.dataset.weight || selectedBadge.textContent.trim()) : null;

    // Disable button during request
    const btn = this;
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Adding...';

    // Send AJAX request
    fetch('/online-sp/cart?action=add', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        product_id: productId,
        sku_id: skuId,
        quantity: quantity,
        unit: unit
      })
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      console.log('Cart response:', data); // Debug log
      if (data.success) {
        // Show success message at bottom right
        const message = data.message || 'Item added to cart successfully!';
        console.log('Showing success message:', message); // Debug log
        showToastMessage(message, 'success');
        // Update cart count in header
        updateCartCount(data.cart_count || 0);
      } else {
        console.log('Showing error message:', data.message); // Debug log
        showToastMessage(data.message || 'Failed to add item to cart', 'error');
      }
    })
    .catch(error => {
      console.error('Add to cart error:', error);
      showToastMessage('Failed to add item to cart. Please try again.', 'error');
    })
    .finally(() => {
      btn.disabled = false;
      btn.textContent = originalText;
    });
  });

  // Function to update cart count in header
  function updateCartCount(count) {
    const cartBadge = document.getElementById('cart-count-badge');
    const cartLink = document.querySelector('.cart-link');
    
    if (count > 0) {
      if (cartBadge) {
        cartBadge.textContent = count;
      } else {
        // Create badge if it doesn't exist
        const iconCart = cartLink.querySelector('.icon-cart');
        if (iconCart) {
          const badge = document.createElement('span');
          badge.id = 'cart-count-badge';
          badge.className = 'cart-count-badge';
          badge.textContent = count;
          iconCart.appendChild(badge);
        }
      }
    } else {
      // Remove badge if count is 0
      if (cartBadge) {
        cartBadge.remove();
      }
    }
  }
})();

// Quantity Control
(function() {
  const quantityInput = document.getElementById('quantity');
  const decreaseBtn = document.querySelector('.quantity-btn.quantity-decrease');
  const increaseBtn = document.querySelector('.quantity-btn.quantity-increase');
  
  if (!quantityInput || !decreaseBtn || !increaseBtn) return;
  
  // Increase quantity
  increaseBtn.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    let currentValue = parseInt(quantityInput.value) || 1;
    quantityInput.value = currentValue + 1;
    quantityInput.dispatchEvent(new Event('change', { bubbles: true }));
  });
  
  // Decrease quantity (minimum 1)
  decreaseBtn.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    let currentValue = parseInt(quantityInput.value) || 1;
    if (currentValue > 1) {
      quantityInput.value = currentValue - 1;
      quantityInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
  });
  
  // Ensure minimum value of 1
  quantityInput.addEventListener('change', function() {
    let value = parseInt(this.value) || 1;
    if (value < 1) {
      this.value = 1;
    }
  });
  
  // Prevent manual input below 1
  quantityInput.addEventListener('input', function() {
    let value = parseInt(this.value);
    if (isNaN(value) || value < 1) {
      this.value = 1;
    }
  });
})();

// SKU Selection and Price Update with URL
(function() {
  const weightBadges = document.querySelectorAll('.weight-badge[data-sku-id]');
  const selectedSkuId = document.getElementById('selected-sku-id');
  const priceElement = document.getElementById('product-price');
  const priceOriginal = document.getElementById('product-price-original');
  const priceStack = document.querySelector('.price-stack');
  const shareBtn = document.querySelector('.share-icon-btn');
  
  if (!weightBadges.length) return;
  
  function formatPrice(price) {
    // Format price to match PHP format_price function (₹ symbol, 0 decimals)
    return '₹' + Math.round(parseFloat(price)).toLocaleString('en-IN');
  }
  
  function updatePrice(badge) {
    const price = parseFloat(badge.dataset.price || 0);
    const originalPrice = parseFloat(badge.dataset.originalPrice || 0);
    const offerPrice = badge.dataset.offerPrice ? parseFloat(badge.dataset.offerPrice) : null;
    const skuId = badge.dataset.skuId;
    
    // Update selected SKU ID
    if (selectedSkuId && skuId) {
      selectedSkuId.value = skuId;
    }
    
    // Update URL with SKU parameter
    const url = new URL(window.location.href);
    if (skuId) {
      url.searchParams.set('sku', skuId);
    } else {
      url.searchParams.delete('sku');
    }
    window.history.replaceState({}, '', url.toString());
    
    // Get current price elements
    const currentPriceEl = document.getElementById('product-price');
    const currentPriceOriginal = document.getElementById('product-price-original');
    const currentPriceStack = document.querySelector('.price-stack');
    
    // Update price display
    if (offerPrice && offerPrice < originalPrice && originalPrice > 0) {
      // Show offer price with original price strikethrough
      const discount = Math.round(((originalPrice - offerPrice) / originalPrice) * 100);
      
      if (currentPriceStack) {
        // Check if price-wrapper exists
        let priceWrapper = currentPriceStack.querySelector('.price-wrapper');
        
        if (!priceWrapper) {
          // Create price wrapper
          currentPriceStack.innerHTML = `
            <div class="price-wrapper">
              <span class="price-regular" id="product-price-original">${formatPrice(originalPrice)}</span>
              <span class="price-accent" id="product-price">${formatPrice(offerPrice)}</span>
              <span class="discount-badge-animated">${discount}% OFF</span>
            </div>
          `;
        } else {
          // Update existing price wrapper
          const originalEl = priceWrapper.querySelector('.price-regular');
          const priceEl = priceWrapper.querySelector('.price-accent');
          const discountEl = priceWrapper.querySelector('.discount-badge-animated');
          
          if (originalEl) originalEl.textContent = formatPrice(originalPrice);
          if (priceEl) priceEl.textContent = formatPrice(offerPrice);
          if (discountEl) {
            discountEl.textContent = discount + '% OFF';
          }
        }
      }
    } else {
      // Show regular price
      if (currentPriceStack) {
        const priceWrapper = currentPriceStack.querySelector('.price-wrapper');
        if (priceWrapper) {
          // Remove wrapper, show simple price
          priceWrapper.outerHTML = `<span class="price-accent" id="product-price">${formatPrice(price)}</span>`;
        } else if (currentPriceEl) {
          currentPriceEl.textContent = formatPrice(price);
        }
      }
    }
  }
  
  // Handle badge clicks
  weightBadges.forEach(badge => {
    badge.addEventListener('click', function() {
      // Remove active class from all badges
      weightBadges.forEach(b => b.classList.remove('active'));
      // Add active class to clicked badge
      this.classList.add('active');
      // Update price and URL
      updatePrice(this);
    });
  });
  
  // Initialize first badge as active and set price
  if (weightBadges.length > 0) {
    const firstBadge = document.querySelector('.weight-badge.active');
    if (firstBadge) {
      updatePrice(firstBadge);
    } else {
      weightBadges[0].classList.add('active');
      updatePrice(weightBadges[0]);
    }
  }
  
  // Check URL for SKU parameter on page load
  const urlParams = new URLSearchParams(window.location.search);
  const skuFromUrl = urlParams.get('sku');
  if (skuFromUrl) {
    const badgeFromUrl = Array.from(weightBadges).find(b => b.dataset.skuId === skuFromUrl);
    if (badgeFromUrl) {
      weightBadges.forEach(b => b.classList.remove('active'));
      badgeFromUrl.classList.add('active');
      updatePrice(badgeFromUrl);
    }
  }
})();

// Toggle Description See More/Less
function toggleDescription() {
  const descriptionElement = document.getElementById('product-full-description');
  const moreText = descriptionElement?.querySelector('.description-more-text');
  const btnSeeMore = document.getElementById('btn-see-more');
  
  if (moreText && btnSeeMore) {
    const isExpanded = btnSeeMore.classList.contains('expanded');
    
    if (isExpanded) {
      // Collapse
      moreText.style.display = 'none';
      btnSeeMore.classList.remove('expanded');
    } else {
      // Expand
      moreText.style.display = 'inline';
      btnSeeMore.classList.add('expanded');
    }
  }
}

// Product Image Gallery - Thumbnail Click Handler
(function() {
  const mainImage = document.getElementById('product-main-image');
  const mainMedia = document.getElementById('product-main-media');
  const thumbButtons = document.querySelectorAll('.media-thumb');
  
  if (thumbButtons.length > 0 && (mainImage || mainMedia)) {
    thumbButtons.forEach(thumb => {
      thumb.addEventListener('click', function() {
        const mediaType = this.dataset.mediaType;
        const mediaSrc = this.dataset.mediaSrc;
        
        if (!mediaSrc) return;
        
        // Remove active class from all thumbs
        thumbButtons.forEach(t => t.classList.remove('active'));
        // Add active class to clicked thumb
        this.classList.add('active');
        
        // Handle image
        if (mediaType === 'image' && mainImage) {
          mainImage.src = mediaSrc;
          // Hide video if it exists
          if (mainMedia) {
            mainMedia.style.display = 'none';
          }
          mainImage.style.display = 'block';
        } 
        // Handle video
        else if (mediaType === 'video' && mainMedia) {
          const source = mainMedia.querySelector('source');
          if (source) {
            source.src = mediaSrc;
            mainMedia.load();
          }
          // Hide image if it exists
          if (mainImage) {
            mainImage.style.display = 'none';
          }
          mainMedia.style.display = 'block';
        }
      });
    });
  }
})();
</script>

