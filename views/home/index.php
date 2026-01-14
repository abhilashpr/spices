<?php
/**
 * Home Page View
 */
// Helpers are already loaded in router.php
if (!function_exists('e')) {
    require_once __DIR__ . '/../../app/helpers/helpers.php';
}
?>
<section class="hero glass-panel" id="shop">
  <div
    class="slider"
    role="region"
    aria-roledescription="carousel"
    aria-label="Featured spice highlights"
    aria-live="polite"
  >
    <?php foreach ($heroSlides as $index => $slide): ?>
      <article
        class="slide <?= $index === 0 ? 'active' : '' ?>"
        id="slide-<?= $index ?>"
        role="tabpanel"
        aria-hidden="<?= $index === 0 ? 'false' : 'true' ?>"
        tabindex="<?= $index === 0 ? '0' : '-1' ?>"
      >
        <div class="hero-copy">
          <?php if (!empty($slide['tag_line'])): ?>
            <span class="tag"><?= e($slide['tag_line']) ?></span>
          <?php endif; ?>
          <h1><?= e($slide['headline']) ?></h1>
          <p><?= e($slide['description']) ?></p>
          <div class="actions">
            <?php if (!empty($slide['primary_label']) && !empty($slide['primary_url'])): ?>
              <a class="button primary" href="<?= e($slide['primary_url']) ?>">
                <?= e($slide['primary_label']) ?>
              </a>
            <?php endif; ?>
            <?php if (!empty($slide['secondary_label']) && !empty($slide['secondary_url'])): ?>
              <a class="button outline" href="<?= e($slide['secondary_url']) ?>">
                <?= e($slide['secondary_label']) ?>
              </a>
            <?php endif; ?>
          </div>
        </div>
        <div class="hero-media">
          <?php
            $orbs = array_filter(array_map('trim', explode(',', (string) ($slide['media_orbs'] ?? ''))));
            foreach ($orbs as $orbClass):
              $class = 'spice-orb ' . e(str_replace(' ', ' ', $orbClass));
              echo '<div class="' . $class . '"></div>';
            endforeach;
          ?>
          <?php if (!empty($slide['card_title']) || !empty($slide['card_subtitle']) || !empty($slide['price'])): ?>
            <div class="floating-card">
              <?php if (!empty($slide['card_title'])): ?>
                <h3><?= e($slide['card_title']) ?></h3>
              <?php endif; ?>
              <?php if (!empty($slide['card_subtitle'])): ?>
                <p><?= e($slide['card_subtitle']) ?></p>
              <?php endif; ?>
              <?php if (!is_null($slide['price'] ?? null)): ?>
                <span class="price"><?= format_price((float) $slide['price']) ?></span>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
  <div class="slider-dots" role="tablist" aria-label="Featured blends">
    <?php foreach ($heroSlides as $index => $slide): ?>
      <button
        class="dot <?= $index === 0 ? 'active' : '' ?>"
        data-slide="<?= $index ?>"
        role="tab"
        aria-selected="<?= $index === 0 ? 'true' : 'false' ?>"
        aria-controls="slide-<?= $index ?>"
        aria-label="Slide <?= $index + 1 ?>"
        tabindex="<?= $index === 0 ? '0' : '-1' ?>"
      ></button>
    <?php endforeach; ?>
  </div>
</section>

<section class="section glass-panel" id="best-sellers">
  <div class="section-header">
    <span class="tag">Best Sellers</span>
    <h2>Beloved blends in every kitchen</h2>
    <p>Handpicked by our chefs and community for their versatility and depth.</p>
  </div>
  <div class="card-grid three-up">
    <?php foreach ($bestSellers as $product): ?>
      <article class="product-card highlight">
        <?php if (!empty($product['main_image'])): ?>
          <?php $imgUrl = get_image_url($product['main_image']); ?>
          <div class="card-image" style="background-image: url('<?= $imgUrl ?>');"></div>
        <?php else: ?>
          <div class="card-image <?= e($product['image_class'] ?? '') ?>"></div>
        <?php endif; ?>
        <h3><?= e($product['name']) ?></h3>
        <p><?= e($product['summary'] ?? '') ?></p>
        <div class="card-footer">
          <?php if (!empty($product['price'])): ?>
            <span class="price"><?= format_price((float) $product['price']) ?></span>
          <?php endif; ?>
          <a class="button ghost" href="<?= url('product.php?slug=' . urlencode($product['slug'])) ?>">
            View details
          </a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="section glass-panel" id="collections">
  <div class="section-header">
    <span class="tag">Curated Collections</span>
    <h2>Blends that tell a story</h2>
    <p>
      Each blend is crafted to highlight the origin terroir and the culinary heritage of the region.
    </p>
  </div>
  <div class="card-grid">
    <?php foreach ($collectionProducts as $product): ?>
      <article class="product-card">
        <?php if (!empty($product['main_image'])): ?>
          <?php $imgUrl = get_image_url($product['main_image']); ?>
          <div class="card-image" style="background-image: url('<?= $imgUrl ?>');"></div>
        <?php else: ?>
          <div class="card-image <?= e($product['image_class'] ?? '') ?>"></div>
        <?php endif; ?>
        <h3><?= e($product['name']) ?></h3>
        <p><?= e($product['summary'] ?? '') ?></p>
        <div class="card-footer">
          <?php if (!empty($product['price'])): ?>
            <span class="price"><?= format_price((float) $product['price']) ?></span>
          <?php endif; ?>
          <a class="button ghost" href="<?= url('product.php?slug=' . urlencode($product['slug'])) ?>">
            View details
          </a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="section glass-panel" id="craft">
  <div class="section-header">
    <span class="tag">Our Craft</span>
    <h2>Designed for discerning chefs</h2>
  </div>
  <div class="feature-grid">
    <div class="feature">
      <h3>Single-Origin Purity</h3>
      <p>
        We partner directly with heritage growers to preserve biodiversity and ensure exceptional quality.
      </p>
    </div>
    <div class="feature">
      <h3>Slow Roasted</h3>
      <p>
        Gentle roasting unlocks deeper aromatics while preserving the delicate oils in each spice.
      </p>
    </div>
    <div class="feature">
      <h3>Sommelier Pairings</h3>
      <p>
        Flavor notes curated by culinary sommeliers to inspire your next signature dish.
      </p>
    </div>
    <div class="feature">
      <h3>Freshness Guarantee</h3>
      <p>
        Small-batch tinning with nitrogen flush keeps blends fragrant to the final spoonful.
      </p>
    </div>
  </div>
</section>

<section class="section glass-panel wide" id="stories">
  <div class="story copy">
    <span class="tag">Stories from the Source</span>
    <h2>From misty peaks to desert caravans</h2>
    <p>
      Follow the journey of each spice from seed to table. Our storytellers travel to the farthest farms—recording culture,
      tradition, and the artisans behind every harvest.
    </p>
    <a class="button primary" href="#">Read the journal</a>
  </div>
  <div class="story media">
    <div class="story-card">
      <h3>Harvesting Saffron in Pampore</h3>
      <p>Golden dawns over Kashmir's saffron valleys.</p>
    </div>
    <div class="story-card">
      <h3>Sea Breeze Peppercorns</h3>
      <p>Monsoon-lashed vines on the Malabar coast.</p>
    </div>
  </div>
</section>

<section class="section glass-panel" id="taste-club">
  <div class="section-header">
    <span class="tag">Taste Club</span>
    <h2>Monthly Flight Subscription</h2>
    <p>
      Seasonal blends delivered to your door with tasting notes, pairing guides, and chef-led workshops.
    </p>
  </div>
  <form class="subscription-form" id="contact">
    <label for="email">Email address</label>
    <div class="input-group">
      <input type="email" id="email" name="email" placeholder="chef@atelier.com" required />
      <button type="submit" class="button primary">Join now</button>
    </div>
    <p class="form-footnote">
      By joining, you agree to receive seasonal updates and invitations.
    </p>
  </form>
</section>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const sliderContainer = document.querySelector('.slider');
    let current = 0;
    let sliderInterval = null;

    const setActive = (index) => {
      slides.forEach((slide, i) => {
        const isActive = i === index;
        slide.classList.toggle('active', isActive);
        slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
        slide.setAttribute('tabindex', isActive ? '0' : '-1');
        slide.style.position = isActive ? 'relative' : 'absolute';
      });
      dots.forEach((dot, i) => {
        const isActive = i === index;
        dot.classList.toggle('active', isActive);
        dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
        dot.setAttribute('tabindex', isActive ? '0' : '-1');
      });
      current = index;
      if (sliderContainer && slides[index]) {
        sliderContainer.style.height = `${slides[index].scrollHeight}px`;
      }
    };

    const startSlider = () => {
      if (sliderInterval || slides.length < 2) return;
      sliderInterval = setInterval(() => {
        const next = (current + 1) % slides.length;
        setActive(next);
      }, 6000);
    };

    const stopSlider = () => {
      if (sliderInterval) {
        clearInterval(sliderInterval);
        sliderInterval = null;
      }
    };

    dots.forEach((dot) => {
      dot.addEventListener('click', () => {
        stopSlider();
        setActive(Number(dot.dataset.slide));
        startSlider();
      });
      dot.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
          event.preventDefault();
          const delta = event.key === 'ArrowRight' ? 1 : -1;
          const next = (Number(dot.dataset.slide) + delta + slides.length) % slides.length;
          stopSlider();
          setActive(next);
          dots[next].focus();
          startSlider();
        }
      });
    });

    if (sliderContainer) {
      sliderContainer.addEventListener('mouseenter', stopSlider);
      sliderContainer.addEventListener('mouseleave', startSlider);
    }

    setActive(0);
    startSlider();
  });
</script>

