<?php
/**
 * Category/Products Listing View
 */
require_once __DIR__ . '/../../app/helpers/helpers.php';

if (!function_exists('format_price')) {
    require_once __DIR__ . '/../../app/helpers/helpers.php';
}
?>
<section class="category-main">
  <!-- Kerala Spices Banner Section -->
  <section class="kerala-banner">
    <div class="banner-image">
      <div class="banner-overlay"></div>
      <div class="banner-content">
        <div class="banner-quote">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"></path>
            <path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"></path>
          </svg>
          <p>"In the spice gardens of Kerala, every leaf whispers ancient secrets, and every pod carries the warmth of the sun."</p>
        </div>
      </div>
    </div>
  </section>

  <section class="category-layout">
    <aside class="glass-panel filters-panel">
      <div class="filters-header">
        <h2>Filters</h2>
        <button type="button" class="button outline small" id="reset-filters">Clear</button>
      </div>
      <form class="filters-form" aria-label="Filter blends">
        <?php 
        $categoriesWithSubcategories = $categoriesWithSubcategories ?? [];
        if (!empty($categoriesWithSubcategories)): 
          foreach ($categoriesWithSubcategories as $category): 
        ?>
          <fieldset class="filter-group">
            <legend><?= e($category['name']) ?></legend>
            <?php if (!empty($category['subcategories'])): ?>
              <?php foreach ($category['subcategories'] as $subcategory): ?>
                <label>
                  <input type="checkbox" name="subcategory" value="<?= e($subcategory['id']) ?>" />
                  <span><?= e($subcategory['name']) ?></span>
                </label>
              <?php endforeach; ?>
            <?php endif; ?>
          </fieldset>
        <?php 
          endforeach; 
        endif; 
        ?>
      </form>
      <div class="active-filters" aria-live="polite" hidden></div>
    </aside>

    <section class="glass-panel results-panel" aria-live="polite">
      <div class="results-header">
        <div>
          <span class="tag">Curated Selection</span>
          <h2>Limited Harvest Collections</h2>
        </div>
        <div class="sort-control">
          <label for="sort-select">Sort by</label>
          <select id="sort-select">
            <option value="featured">Featured</option>
            <option value="price-asc">Price: Low to High</option>
            <option value="price-desc">Price: High to Low</option>
            <option value="heat">Heat Level</option>
          </select>
        </div>
      </div>

      <div class="card-grid" id="product-grid">
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $product): ?>
            <?php 
            $productImage = null;
            if (!empty($product['main_image'])) {
              $productImage = get_image_url($product['main_image']);
            }
            $minPrice = $product['min_price'] ?? null;
            $minOfferPrice = $product['min_offer_price'] ?? null;
            $productTitle = $product['title'] ?? $product['name'] ?? '';
            $productSummary = $product['summary'] ?? '';
            ?>
            <article class="product-card" 
                     data-product-id="<?= e($product['id']) ?>"
                     data-name="<?= e(strtolower($productTitle)) ?>"
                     data-price="<?= $minPrice ?? 0 ?>">
              <?php 
              $hasOffer = ($minOfferPrice !== null && $minPrice !== null && $minOfferPrice < $minPrice);
              $discountPercent = 0;
              if ($hasOffer && $minPrice > 0) {
                $discountPercent = round((($minPrice - $minOfferPrice) / $minPrice) * 100);
              }
              $isOutOfStock = isset($product['is_out_of_stock']) && $product['is_out_of_stock'];
              ?>
              <?php if ($productImage): ?>
                <div class="card-image" style="background-image: url('<?= e($productImage) ?>');">
                  <?php if ($hasOffer && $discountPercent > 0): ?>
                    <span class="offer-badge">-<?= $discountPercent ?>%</span>
                  <?php endif; ?>
                  <?php if ($isOutOfStock): ?>
                    <div class="out-of-stock-overlay">
                      <span class="out-of-stock-text">Out of Stock</span>
                    </div>
                  <?php else: ?>
                    <div class="image-hover-overlay">
                      <a href="<?= url('product?slug=' . urlencode($product['slug'])) ?>" class="overlay-btn view-details-btn" title="View Details">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                          <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                      </a>
                      <button type="button" class="overlay-btn wishlist-btn" data-product-id="<?= e($product['id']) ?>" title="Add to Wishlist">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                      </button>
                    </div>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <div class="card-image">
                  <?php if ($hasOffer && $discountPercent > 0): ?>
                    <span class="offer-badge">-<?= $discountPercent ?>%</span>
                  <?php endif; ?>
                  <?php if ($isOutOfStock): ?>
                    <div class="out-of-stock-overlay">
                      <span class="out-of-stock-text">Out of Stock</span>
                    </div>
                  <?php else: ?>
                    <div class="image-hover-overlay">
                      <a href="<?= url('product?slug=' . urlencode($product['slug'])) ?>" class="overlay-btn view-details-btn" title="View Details">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                          <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                      </a>
                      <button type="button" class="overlay-btn wishlist-btn" data-product-id="<?= e($product['id']) ?>" title="Add to Wishlist">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                      </button>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
              <h3><?= e($productTitle) ?></h3>
              <?php if ($productSummary): ?>
                <p><?= e($productSummary) ?></p>
              <?php elseif (!empty($product['description'])): ?>
                <p><?= e(mb_substr(strip_tags($product['description']), 0, 100)) ?><?= mb_strlen(strip_tags($product['description'])) > 100 ? '...' : '' ?></p>
              <?php endif; ?>
              <?php 
              $rating = $product['rating'] ?? 0;
              $reviewCount = $product['review_count'] ?? 0;
              $roundedRating = round($rating); // Round to nearest integer for star display
              ?>
              <div class="product-rating">
                <div class="rating-stars">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if ($i <= $roundedRating): ?>
                      <span class="star filled">★</span>
                    <?php else: ?>
                      <span class="star">☆</span>
                    <?php endif; ?>
                  <?php endfor; ?>
                </div>
                <span class="rating-count">(<?= $reviewCount ?>)</span>
              </div>
              <div class="card-footer">
                <div class="price-section">
                  <?php if ($minPrice !== null): ?>
                    <?php if ($hasOffer): ?>
                      <span class="price-line">
                        <span class="price original-price-strike"><?= format_price($minPrice) ?></span>
                        <span class="price offer-price"><?= format_price($minOfferPrice) ?></span>
                      </span>
                    <?php else: ?>
                      <span class="price"><?= format_price($minPrice) ?></span>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="price">Price on request</span>
                  <?php endif; ?>
                </div>
                <?php if ($isOutOfStock): ?>
                  <a class="button btn-out-of-stock" href="<?= url('product?slug=' . urlencode($product['slug'])) ?>">
                    View Details
                  </a>
                <?php else: ?>
                  <button class="button btn-add-to-cart" type="button" data-product-id="<?= e($product['id']) ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="9" cy="21" r="1"></circle>
                      <circle cx="20" cy="21" r="1"></circle>
                      <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    Add to cart
                  </button>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state">
            <div class="empty-state-icon">🔍</div>
            <h3>No products found.</h3>
            <p><?= $selectedSubcategory ?? false ? 'No products available in this category yet.' : 'Check back later for new spice blends.' ?></p>
            <?php if ($selectedSubcategory ?? false): ?>
              <a href="<?= url('categories') ?>" class="button primary">View All Products</a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

    </section>
  </section>
</section>

<script>
  const filtersForm = document.querySelector('.filters-form');
  const checkboxes = filtersForm ? filtersForm.querySelectorAll("input[type='checkbox'][name='subcategory']") : [];
  const activeFiltersContainer = document.querySelector('.active-filters');
  const resetButton = document.getElementById('reset-filters');
  const sortSelect = document.getElementById('sort-select');
  let isProgrammaticChange = false;

  // Navigate to filtered URL with multiple subcategories
  const navigateToFilter = () => {
    // Get all checked checkboxes
    const selectedValues = Array.from(checkboxes)
      .filter(cb => cb.checked)
      .map(cb => cb.value);
    
    // Get current URL
    const url = new URL(window.location.href);
    const newParams = new URLSearchParams();
    
    // Copy all existing params except filter-related ones
    for (const [key, value] of url.searchParams.entries()) {
      if (key !== 'filter' && key !== 'filter[]' && key !== 'subcategory') {
        newParams.append(key, value);
      }
    }
    
    // Add selected filter values as filter[] array parameters
    selectedValues.forEach(value => {
      newParams.append('filter[]', value);
    });
    
    // Reconstruct URL with new params
    const newUrl = url.origin + url.pathname;
    const paramString = newParams.toString();
    window.location.href = paramString ? `${newUrl}?${paramString}` : newUrl;
  };

  // Update active filter badges
  const updateBadges = () => {
    if (!activeFiltersContainer) return;
    
    activeFiltersContainer.innerHTML = '';
    const selectedSubcategories = Array.from(checkboxes)
      .filter(cb => cb.checked)
      .map(cb => {
        const label = cb.closest('label');
        const name = label ? label.querySelector('span')?.textContent?.trim() || cb.value : cb.value;
        return { value: cb.value, name: name };
      });

    selectedSubcategories.forEach(({ value, name }) => {
      const badge = document.createElement('button');
      badge.className = 'filter-chip';
      badge.type = 'button';
      badge.dataset.value = value;
      badge.innerHTML = `${name}<span aria-hidden="true"> ×</span>`;
      badge.title = `Remove ${name}`;
      badge.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const checkbox = filtersForm.querySelector(`input[name='subcategory'][value='${CSS.escape(value)}']`);
        if (checkbox) {
          isProgrammaticChange = true;
          checkbox.checked = false;
          isProgrammaticChange = false;
          navigateToFilter();
        }
      });
      activeFiltersContainer.appendChild(badge);
    });

    activeFiltersContainer.toggleAttribute('hidden', selectedSubcategories.length === 0);
  };

  // Sort products by selected option
  const sortCards = () => {
    if (!sortSelect) return;
    const grid = document.getElementById('product-grid');
    if (!grid) return;
    const items = Array.from(grid.children).filter(el => !el.classList.contains('empty-state'));
    
    if (items.length === 0) return;

    let comparator = () => 0;

    if (sortSelect.value === 'price-asc') {
      comparator = (a, b) => Number(a.dataset.price || 0) - Number(b.dataset.price || 0);
    } else if (sortSelect.value === 'price-desc') {
      comparator = (a, b) => Number(b.dataset.price || 0) - Number(a.dataset.price || 0);
    } else if (sortSelect.value === 'name') {
      comparator = (a, b) => (a.dataset.name || '').localeCompare(b.dataset.name || '');
    }

    items.sort(comparator).forEach((item) => grid.appendChild(item));
  };

  // Handle checkbox changes - navigate to filtered URL with multi-select
  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', (e) => {
      // Only navigate if this is a user-initiated change, not programmatic
      if (!isProgrammaticChange) {
        navigateToFilter();
      }
    });
  });

  // Clear all filters and show all products
  const clearAll = () => {
    isProgrammaticChange = true;
    checkboxes.forEach((checkbox) => {
      checkbox.checked = false;
    });
    isProgrammaticChange = false;
    
    // Navigate to base URL without any filter parameters
    const url = new URL(window.location.href);
    const newParams = new URLSearchParams();
    
    // Copy all existing params except filter-related ones
    for (const [key, value] of url.searchParams.entries()) {
      if (key !== 'filter' && key !== 'filter[]' && key !== 'subcategory') {
        newParams.append(key, value);
      }
    }
    
    // Reconstruct URL
    const newUrl = url.origin + url.pathname;
    const paramString = newParams.toString();
    window.location.href = paramString ? `${newUrl}?${paramString}` : newUrl;
  };

  if (resetButton) {
    resetButton.addEventListener('click', (e) => {
      e.preventDefault();
      clearAll();
    });
  }

  // Handle sort changes
  if (sortSelect) {
    sortSelect.addEventListener('change', () => {
      sortCards();
    });
  }

  // Preselect subcategories from URL parameter (supports multiple filters)
  const preselectFilters = () => {
    if (!filtersForm) return;
    
    const params = new URLSearchParams(window.location.search);
    
    // Get all filter values (supports both filter[] array and single filter)
    let filterValues = [];
    
    // Check for filter[] array parameter (multi-select)
    if (params.getAll('filter[]').length > 0) {
      filterValues = params.getAll('filter[]');
    } 
    // Check for single filter parameter
    else if (params.get('filter')) {
      filterValues = [params.get('filter')];
    }
    // Fallback to subcategory for backward compatibility
    else if (params.get('subcategory')) {
      filterValues = [params.get('subcategory')];
    }
    
    if (filterValues.length > 0) {
      // Check all matching checkboxes (multi-select)
      isProgrammaticChange = true;
      checkboxes.forEach(checkbox => {
        checkbox.checked = filterValues.includes(checkbox.value);
      });
      isProgrammaticChange = false;
      updateBadges();
    } else {
      // No filter in URL, uncheck all
      isProgrammaticChange = true;
      checkboxes.forEach(checkbox => {
        checkbox.checked = false;
      });
      isProgrammaticChange = false;
      updateBadges();
    }
    
    sortCards();
  };

  // Initialize on page load
  preselectFilters();

  // Add to cart functionality
  document.querySelectorAll('.btn-add-to-cart').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const productId = this.dataset.productId;
      
      // Add to cart logic - you can implement your cart functionality here
      console.log('Add to cart:', productId);
      
      // Show success message (implement your cart API call here)
      this.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg> Added';
      this.style.background = 'linear-gradient(135deg, #28a372, #1d8a5f)';
      setTimeout(() => {
        this.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg> Add to cart';
        this.style.background = 'linear-gradient(135deg, #32c68d, #28a372)';
      }, 2000);
    });
  });

  // Show toast message notification
  function showToastMessage(message, type = 'success') {
    // Remove existing toast if any
    const existingToast = document.querySelector('.toast-message');
    if (existingToast) {
      existingToast.remove();
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'toast-message' + (type === 'error' ? ' error' : '');
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Show toast immediately
    toast.classList.add('show');
    
    // Hide and remove toast after 3 seconds
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    }, 3000);
  }

  // Wishlist button functionality
  document.querySelectorAll('.wishlist-btn').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const productId = this.dataset.productId;
      const btn = this;
      
      if (!productId) return;
      
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
        if (data.login_required) {
          // Show login required toast
          showToastMessage(data.message || 'Please login then only wishlist', 'error');
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
  });

</script>
<style>
.category-main {
  width: 100%;
}

/* Kerala Spices Banner Section */
.kerala-banner {
  width: 100%;
  margin-bottom: 60px;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 12px 40px rgba(21, 66, 55, 0.2);
}

.kerala-banner .banner-image {
  position: relative;
  min-height: 400px;
  background-image: url('https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=1920&q=80');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Pattern overlay for texture */
.kerala-banner .banner-image::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    repeating-linear-gradient(45deg, transparent, transparent 2px, rgba(255,255,255,.03) 2px, rgba(255,255,255,.03) 4px),
    repeating-linear-gradient(-45deg, transparent, transparent 2px, rgba(0,0,0,.03) 2px, rgba(0,0,0,.03) 4px);
  pointer-events: none;
  z-index: 1;
}

.kerala-banner .banner-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(9, 52, 42, 0.75), rgba(21, 66, 55, 0.7));
  z-index: 2;
}

.kerala-banner .banner-content {
  position: relative;
  z-index: 3;
  max-width: 900px;
  padding: 60px 40px;
  text-align: center;
  color: white;
  width: 100%;
}

.kerala-banner .banner-quote {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 24px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
  padding: 40px 50px;
  border-radius: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}

.kerala-banner .banner-quote svg {
  flex-shrink: 0;
  color: rgba(255, 255, 255, 0.9);
  opacity: 0.95;
}

.kerala-banner .banner-quote p {
  font-size: 1.5rem;
  font-style: italic;
  line-height: 1.8;
  margin: 0;
  color: white;
  font-family: 'Playfair Display', serif;
  font-weight: 500;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  letter-spacing: 0.02em;
}

/* Responsive adjustments for banner */
@media (max-width: 1024px) {
  .kerala-banner .banner-image {
    min-height: 350px;
  }
  
  .kerala-banner .banner-content {
    padding: 50px 32px;
  }
  
  .kerala-banner .banner-quote {
    padding: 35px 40px;
    gap: 20px;
  }
  
  .kerala-banner .banner-quote p {
    font-size: 1.3rem;
  }
}

@media (max-width: 768px) {
  .kerala-banner {
    margin-bottom: 40px;
    border-radius: 16px;
  }
  
  .kerala-banner .banner-image {
    min-height: 300px;
  }
  
  .kerala-banner .banner-content {
    padding: 40px 24px;
  }
  
  .kerala-banner .banner-quote {
    flex-direction: column;
    padding: 30px 28px;
    gap: 16px;
  }
  
  .kerala-banner .banner-quote svg {
    width: 28px;
    height: 28px;
  }
  
  .kerala-banner .banner-quote p {
    font-size: 1.15rem;
    line-height: 1.7;
  }
}

@media (max-width: 640px) {
  .kerala-banner .banner-image {
    min-height: 280px;
  }
  
  .kerala-banner .banner-content {
    padding: 32px 20px;
  }
  
  .kerala-banner .banner-quote {
    padding: 24px 20px;
    gap: 12px;
  }
  
  .kerala-banner .banner-quote svg {
    width: 24px;
    height: 24px;
  }
  
  .kerala-banner .banner-quote p {
    font-size: 1rem;
    line-height: 1.6;
  }
}

.category-layout {
  width: 100%;
}

.results-panel {
  width: 100%;
}

/* Card grid - 3 columns per row matching home page */
#product-grid.card-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

/* Responsive: 2 columns on tablets */
@media (max-width: 1024px) {
  #product-grid.card-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 28px;
  }
}

/* Responsive: 1 column on mobile */
@media (max-width: 640px) {
  #product-grid.card-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }
}

/* Product card - subtle rounded corners on all sides */
#product-grid .product-card {
  padding: 0;
  border-radius: 12px;
  overflow: hidden;
}

/* Card image - full fit flush with article edges with subtle rounded top corners */
#product-grid .product-card .card-image {
  position: relative;
  width: 100%;
  height: 240px;
  border-radius: 12px 12px 0 0;
  margin: 0;
  padding: 0;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  overflow: hidden;
  transition: transform 0.6s ease;
}

/* Rounded bottom corners for the card footer area */
#product-grid .product-card .card-footer {
  border-radius: 0 0 12px 12px;
}

/* Add padding to content area instead */
#product-grid .product-card h3 {
  padding: 20px 28px 0 28px;
  margin-bottom: 8px;
}

/* Product rating stars - positioned above price */
#product-grid .product-card .product-rating {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0 28px 12px 28px;
  margin: 0;
  margin-top: auto;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

#product-grid .product-card .product-rating .rating-stars {
  display: flex;
  gap: 2px;
  align-items: center;
}

#product-grid .product-card .product-rating .star {
  font-size: 1.1rem;
  color: rgba(9, 52, 42, 0.25);
  line-height: 1;
  transition: color 0.2s ease;
  display: inline-block;
}

#product-grid .product-card .product-rating .star.filled {
  color: #ffc107;
}


#product-grid .product-card .product-rating .rating-count {
  font-size: 0.85rem;
  color: rgba(9, 52, 42, 0.6);
  font-weight: 500;
}

#product-grid .product-card p {
  padding: 0 28px;
  margin-bottom: 20px;
}

/* Card footer padding and layout */
#product-grid .product-card .card-footer {
  padding: 16px 28px 28px 28px;
  margin-top: 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  border-top: none;
}

/* Hover zoom effect on image */
#product-grid .product-card:hover .card-image {
  transform: scale(1.05);
}

/* Image hover overlay - only at bottom with complete overlay layer */
#product-grid .product-card .card-image {
  position: relative;
  overflow: hidden;
}

#product-grid .product-card .image-hover-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 80px;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.85), rgba(0, 0, 0, 0.7), transparent);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  display: flex;
  gap: 16px;
  justify-content: center;
  align-items: center;
  opacity: 0;
  transform: translateY(100%);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 10;
}

#product-grid .product-card:hover .image-hover-overlay {
  opacity: 1;
  transform: translateY(0);
}

/* Overlay buttons - icon only, compact design */
#product-grid .product-card .image-hover-overlay .overlay-btn {
  background: rgba(255, 255, 255, 0.95);
  color: #09342a;
  border: none;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
  position: relative;
  overflow: hidden;
}

/* Button hover animation */
#product-grid .product-card .image-hover-overlay .overlay-btn::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(50, 198, 141, 0.2);
  transform: translate(-50%, -50%);
  transition: width 0.4s ease, height 0.4s ease;
}

#product-grid .product-card .image-hover-overlay .overlay-btn:hover::before {
  width: 100%;
  height: 100%;
}

#product-grid .product-card .image-hover-overlay .overlay-btn:hover {
  background: white;
  transform: translateY(-4px) scale(1.1);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
}

#product-grid .product-card .image-hover-overlay .overlay-btn:active {
  transform: translateY(-2px) scale(1.05);
}

/* Icon animation */
#product-grid .product-card .image-hover-overlay .overlay-btn svg {
  width: 22px;
  height: 22px;
  position: relative;
  z-index: 1;
  transition: transform 0.3s ease;
}

#product-grid .product-card .image-hover-overlay .overlay-btn:hover svg {
  transform: scale(1.15) rotate(5deg);
}

/* View details button specific */
#product-grid .product-card .image-hover-overlay .view-details-btn:hover {
  color: #32c68d;
}

/* Wishlist button specific styling */
#product-grid .product-card .image-hover-overlay .wishlist-btn:hover {
  color: #dc3545;
}

#product-grid .product-card .image-hover-overlay .wishlist-btn:hover::before {
  background: rgba(220, 53, 69, 0.15);
}

#product-grid .product-card .image-hover-overlay .wishlist-btn.added {
  background: #dc3545;
  color: white;
}

#product-grid .product-card .image-hover-overlay .wishlist-btn.added svg {
  fill: currentColor;
  animation: heartBeat 0.6s ease;
}

@keyframes heartBeat {
  0%, 100% {
    transform: scale(1);
  }
  25% {
    transform: scale(1.3);
  }
  50% {
    transform: scale(1.1);
  }
}

/* Responsive adjustments for overlay buttons */
@media (max-width: 640px) {
  #product-grid .product-card .image-hover-overlay {
    height: 70px;
    gap: 12px;
  }
  
  #product-grid .product-card .image-hover-overlay .overlay-btn {
    width: 44px;
    height: 44px;
  }
  
  #product-grid .product-card .image-hover-overlay .overlay-btn svg {
    width: 20px;
    height: 20px;
  }
}

/* Offer badge - top left with bright color and animation */
#product-grid .product-card .card-image .offer-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  background: linear-gradient(135deg, #ff6b35, #ff8c42);
  color: white;
  padding: 8px 12px;
  border-radius: 50%;
  font-size: 0.85rem;
  font-weight: 900;
  z-index: 15;
  line-height: 1;
  box-shadow: 0 4px 12px rgba(255, 107, 53, 0.5);
  min-width: 52px;
  min-height: 52px;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: pulse-offer 2s ease-in-out infinite;
  border: 2px solid rgba(255, 255, 255, 0.3);
}

@keyframes pulse-offer {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.5);
  }
  50% {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.7);
  }
}

/* Out-of-stock overlay - gray shade design */
#product-grid .product-card .card-image .out-of-stock-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(108, 117, 125, 0.75);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 5;
  border-radius: 12px 12px 0 0;
  backdrop-filter: blur(2px);
}

#product-grid .product-card .card-image .out-of-stock-overlay .out-of-stock-text {
  color: white;
  font-size: 1rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  padding: 12px 24px;
  background: rgba(73, 80, 87, 0.95);
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
  border: 2px solid rgba(255, 255, 255, 0.2);
}

/* Price section - single line with strikethrough and offer price */
#product-grid .product-card .card-footer .price-section {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

#product-grid .product-card .card-footer .price-line {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

#product-grid .product-card .card-footer .price {
  font-family: 'Playfair Display', serif;
  font-size: 1.4rem;
  font-weight: 700;
  color: var(--accent);
  line-height: 1.2;
}

#product-grid .product-card .card-footer .original-price-strike {
  font-size: 1.1rem;
  color: rgba(9, 52, 42, 0.5);
  font-weight: 600;
  text-decoration: line-through;
}

#product-grid .product-card .card-footer .offer-price {
  font-size: 1.5rem;
  color: #dc3545;
  font-weight: 800;
}

/* Add to cart button - nice green color - bigger size */
#product-grid .product-card .card-footer .btn-add-to-cart {
  background: linear-gradient(135deg, #32c68d, #28a372);
  color: white;
  border: none;
  padding: 14px 28px;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(50, 198, 141, 0.3);
  min-height: 44px;
  min-width: 140px;
  justify-content: center;
}

#product-grid .product-card .card-footer .btn-add-to-cart:hover {
  background: linear-gradient(135deg, #28a372, #1d8a5f);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(50, 198, 141, 0.4);
}

#product-grid .product-card .card-footer .btn-add-to-cart:active {
  transform: translateY(0);
}

#product-grid .product-card .card-footer .btn-add-to-cart svg {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

/* Out of stock button */
#product-grid .product-card .card-footer .btn-out-of-stock {
  background: rgba(220, 53, 69, 0.1);
  color: #dc3545;
  border: 2px solid #dc3545;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

#product-grid .product-card .card-footer .btn-out-of-stock:hover {
  background: #dc3545;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

/* Empty state */
.empty-state-icon {
  font-size: 3rem;
  margin-bottom: 15px;
}

.empty-state h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1.8rem;
  color: #09342a;
  margin: 0 0 10px 0;
}

.empty-state p {
  color: rgba(9, 52, 42, 0.7);
  font-size: 1rem;
  margin: 0 0 20px 0;
}
</style>


